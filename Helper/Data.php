<?php
namespace Pepperjam\Network\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App\Helper\Context;
use \Magento\Sales\Api\Data\OrderInterface;
use \Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class Data extends AbstractHelper
{
    const TRACKING_BASIC = 'basic';
    const TRACKING_ITEMIZED = 'itemized';
    const TRACKING_DYNAMIC = 'dynamic';

    const TRANSACTION_LEAD = 'lead';
    const TRANSACTION_SALE = 'sale';

    public $_collectionFactory;

    public function __construct(Context $context, CollectionFactory $collectionFactory) {
        parent::__construct($context);

        $this->_collectionFactory = $collectionFactory;
    }

    public function formatMoney($amount) {
        return number_format($amount, 2, '.', '');
    }

    public function getCommissioningCategory($item) {
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

    public function isNewToFile($order) {
        // Customers are being identified by email
        $customerEmail = $order->getCustomerEmail();
        $createdAt = $order->getCreatedAt();

        $orderCollection = $this->_collectionFactory->create();
        $orderCollection->addFieldToFilter(OrderInterface::CUSTOMER_EMAIL, $customerEmail);
        $orderCollection->addFieldToFilter(OrderInterface::CREATED_AT, ['lt' => $createdAt]);
        $orderCollection->load();

        return (boolean) $orderCollection->getSize() == 0;
    }
}
