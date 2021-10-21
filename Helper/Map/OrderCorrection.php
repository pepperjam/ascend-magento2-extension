<?php
namespace Pepperjam\Network\Helper\Map;

use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Framework\App\Helper\AbstractHelper;

use Pepperjam\Network\Helper\Config;
use Pepperjam\Network\Helper\Data;

class OrderCorrection extends AbstractHelper
{
    const FIELD_CATEGORY = 'category';
    const FIELD_ITEM_ID = 'item_id';
    const FIELD_ITEM_QUANTITY = 'item_quantity';
    const FIELD_ITEM_PRICE = 'item_price';
    const FIELD_NEW_TO_FILE = 'new_to_file';
    const FIELD_ORDER_AMOUNT = 'order_amount';
    const FIELD_ORDER_ID = 'order_id';
    const FIELD_PROGRAM_ID = 'program_id';
    const FIELD_REASON = 'reason';
    const FIELD_TRANSACTION_TYPE = 'transaction_type';
    const FIELD_CURRENCY = 'currency';

    const REASON = 8;

    protected $config;

    protected $helper;

    public function __construct(Config $config, Data $helper)
    {
        $this->config = $config;
        $this->helper = $helper;
    }

	// @codingStandardsIgnoreStart - Sometimes it's just gotta be a switch statement
	public function get($item, $attribute)
    {
        switch ($attribute) {
            case self::FIELD_CATEGORY:
                return $this->getCategory($item);
            case self::FIELD_ITEM_ID:
                return $this->getItemId($item);
            case self::FIELD_ITEM_QUANTITY:
                return $this->getItemQuantity($item);
            case self::FIELD_ITEM_PRICE:
                return $this->getItemPrice($item);
            case self::FIELD_NEW_TO_FILE:
                return $this->getNewToFile($item);
            case self::FIELD_ORDER_AMOUNT:
                return $this->getOrderAmount($item);
            case self::FIELD_ORDER_ID:
                return $this->getOrderId($item);
            case self::FIELD_PROGRAM_ID:
                return $this->config->getProgramId();
            case self::FIELD_REASON:
                return self::REASON;
            case self::FIELD_TRANSACTION_TYPE:
                return $this->getTransactionType();
            case self::FIELD_CURRENCY:
                return $item->getOrder()->getData('order_currency_code');
        }
    }
	// @codingStandardsIgnoreEnd

    public function getCategory($item)
    {
        return $this->helper->getCommissioningCategory($item);
    }

    protected function getDiscountedItemPrice($item)
    {
        // tread bundle items as 0.00 total as their total will be represented by
        // the total of their children products
        if ($item->getProduct() && $item->getProduct()->getTypeId() == ProductType::TYPE_BUNDLE) {
            return 0;
        }

        // return price if everything was refunded or canceled
        if ($this->getItemQuantity($item) == 0) {
            $discount = $item->getDiscountPercent() ? $item->getDiscountPercent() * $item->getPrice() / 100 : 0;
            return $item->getPrice() - $discount;
        }

        // don't allow negative amounts - could happen if a discounted item was cancelled
        $price = $item->getPrice() - (
                ($item->getDiscountAmount() - $item->getDiscountRefunded()) / $this->getItemQuantity($item)
            );
        return max(0, $price);
    }

    public function getItemId($item)
    {
        return urlencode($this->helper->getProductItemId($item));
    }

    public function getItemOrderId($item)
    {
        return $this->getOrderId($item);
    }

    public function getItemQuantity($item)
    {
        return $item->getQtyOrdered() - $item->getQtyRefunded() - $item->getQtyCanceled();
    }

    public function getItemPrice($item)
    {
        if ($this->config->getTransactionType() === Data::TRANSACTION_LEAD
            || $item->getData('qty_canceled') == $item->getData('qty_ordered')) {
            return 0;
        }

        return $this->helper->formatMoney($this->getDiscountedItemPrice($item));
    }

    public function getNewToFile($item)
    {
        return (int) $this->helper->isNewToFile($item->getOrder());
    }

    public function getOrderAmount($item)
    {
        if ($this->config->getTransactionType() === Data::TRANSACTION_LEAD) {
            return 0;
        }

        return max(
            0,
            ($item->getBaseSubtotal() + $item->getBaseDiscountAmount()) -
            ($item->getBaseSubtotalRefunded() + $item->getBaseDiscountRefunded()) -
            ($item->getBaseSubtotalCanceled() + $item->getBaseDiscountRefunded())
        );
    }

    public function getOrderId($item)
    {
        if ($item->getOriginalIncrementId()) {
            return $item->getOriginalIncrementId();
        } else {
            return $item->getIncrementId();
        }
    }

    public function getTransactionType()
    {
        return $this->config->getTransactionType();
    }
}
