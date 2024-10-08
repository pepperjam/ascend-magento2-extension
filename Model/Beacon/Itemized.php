<?php
namespace Pepperjam\Network\Model\Beacon;

use Magento\Bundle\Model\Product\Price;
use Magento\Catalog\Model\Product\Type as ProductType;

use Pepperjam\Network\Model\Beacon;

class Itemized extends Beacon
{
    protected $couponKey = 'PROMOCODE';
    protected $priceKey = 'AMOUNT';
    protected $quantityKey = 'QTY';
    protected $skuKey = 'ITEM';

    public function getUrl()
    {
        $params = $this->orderParams();
        $params = $this->getCouponCode($params);
        $params = $this->addItems($params);
        $params = $this->addCampaign($params);
        $params = $this->addPlatform($params);
        $params = $this->addSignature($params);

        return $this->config->getBeaconBaseUrl() . '?' . http_build_query($params);
    }

    protected function orderParams()
    {
        return [
            'PID' => $this->config->getProgramId(),
            'OID' => $this->order->getIncrementId(),
            'INT' => $this->config->getInt(),
        ];
    }

    protected function addItems($params)
    {
        $itemIndex = 1;
        foreach ($this->order->getAllItems() as $item) {
            // remove complex sub-items as done in vendor/magento/module-sales/view/frontend/templates/order/items.phtml
            if ($item->getParentItem()) continue;

            $position = $this->getPosition($params, $item);
            if ($position) {
                // addexisting should be deprecated as no sub-items should be added.
                $params = $this->existingItem($params, $item, $position);
            } else {
                $params = $this->newItem($params, $item, $itemIndex);
                $itemIndex++; // Only increment after adding a new item
            }
        }

        $params = $this->averageItemAmount($params, $itemIndex);

        return $params;
    }

    protected function getPosition($params, $item)
    {
        $sku = $this->getSku($item);
        $key = array_search($sku, $params, true);

        if ($key) {
            return (int) str_replace($this->skuKey, '', $key);
        } else {
            return false;
        }
    }

    protected function newItem($params, $item, $itemIndex)
    {
        $sku = $this->getSku($item);
        $params[$this->skuKey . $itemIndex] = $sku;
        $params[$this->quantityKey . $itemIndex] = $this->getQuantity($item);
        $params[$this->priceKey . $itemIndex] = $this->getPrice($item);

        return $params;
    }

    /**
     * @deprecated
     */
    protected function existingItem($params, $item, $itemIndex)
    {
        $qty = $this->getQuantity($item);
        if (!$qty) return $params;

        $params[$this->quantityKey . $itemIndex] += $this->getQuantity($item);
        $priceKey = $this->priceKey . $itemIndex;
        $params[$priceKey] = $this->helper->formatMoney($params[$priceKey] + $this->getPrice($item));
        return $params;
    }

    protected function getQuantity($item)
    {
        return (int) $item->getQtyOrdered();
    }

    protected function getPrice($item)
    {
        if ($item->getProduct()
            && $item->getProduct()->getTypeId() === ProductType::TYPE_BUNDLE
            && $item->getProduct()->getPriceType() === Price::PRICE_TYPE_DYNAMIC
        ) {
            return '0.00';
        } else {

            $itemTotal = (float)$item->getRowTotal();
            $orderTotal = (float)$this->order->getSubtotal();
            $orderDiscount = -(float)$this->order->getDiscountAmount();

            $itemDiscount = $item->getDiscountAmount();
            $return_old = $itemTotal - $itemDiscount;

            $itemCalculateddiscount = 1;
            if ($orderDiscount > 0) {
                $itemCalculateddiscount = 1 - ($orderDiscount / $orderTotal);
            }

            $return = $itemTotal * $itemCalculateddiscount;
            return $return;
        }
    }

    protected function averageItemAmount($params, $itemIndex)
    {
        for ($i = 1; $i < $itemIndex; $i++) {
            $averageAmount = 0;
            if ($params[$this->quantityKey.$i] > 0) {
                $averageAmount = $params[$this->priceKey.$i]/$params[$this->quantityKey.$i];
            }

            $params[$this->priceKey.$i] = $this->helper
                ->formatMoney($averageAmount);
        }

        return $params;
    }

    protected function getSku($item) {
        return $this->helper->getProductItemId($item);
    }
}
