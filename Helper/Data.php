<?php
namespace Pepperjam\Network\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;


class Data extends AbstractHelper
{
    const TRACKING_BASIC = 'basic';
    const TRACKING_ITEMIZED = 'itemized';
    const TRACKING_DYNAMIC = 'dynamic';

    const TRANSACTION_LEAD = 'lead';
    const TRANSACTION_SALE = 'sale';

    public $collectionFactory;

    public $config;

    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        \Pepperjam\Network\Helper\Config $config
    ) {
        parent::__construct($context);

        $this->collectionFactory = $collectionFactory;
        $this->config = $config;
    }

    public function formatMoney($amount)
    {
        return number_format($amount, 2, '.', '');
    }

    public function getCommissioningCategory($item)
    {
        if (!$item->getProduct()) return 0;
        
        $category = $item->getProduct()->getCommissioningCategory();
        if ($category == '' || $category == null) {
            $categoryIds = $item->getProduct()->getCategoryIds();
            // if there are any categories, grab the first
            if (!empty($categoryIds)) {
                $category = $categoryIds[0];
            } else {
                $category = 0;
            }
        }

        return $category;
    }

    public function isNewToFile($order)
    {
        // Customers are being identified by email
        $customerEmail = $order->getCustomerEmail();
        $createdAt = $order->getCreatedAt();

        $orderCollection = $this->collectionFactory->create();
        $orderCollection->addFieldToFilter(OrderInterface::CUSTOMER_EMAIL, $customerEmail);
        $orderCollection->addFieldToFilter(OrderInterface::CREATED_AT, ['lt' => $createdAt]);
        $orderCollection->load();

        return (boolean) $orderCollection->getSize() == 0;
    }

    public function getProductItemId($item)
    {
        $id = $item->getSku();
        if (array_key_exists('options', $item->getProductOptions())) {
            foreach ($item->getProductOptions()['options'] as $option) {
                $id .= '-'.$option['value'];
            }
        }
        if (strlen($id) >= $this->config->getBeaconProductIdMaxSize()) {
            $id = $item->getSku(). '-'. md5($id);
        }
        return $id;
    }
}
