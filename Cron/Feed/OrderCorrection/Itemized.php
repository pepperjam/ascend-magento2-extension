<?php
namespace Pepperjam\Network\Cron\Feed\OrderCorrection;

use Magento\Store\Model\Store;
use Magento\Store\Model\StoreRepository;
use Psr\Log\LoggerInterface;

use Pepperjam\Network\Cron\Feed\OrderCorrection;
use Pepperjam\Network\Helper\Config;
use Pepperjam\Network\Helper\Map\OrderCorrection as Map;
use Pepperjam\Network\Model\ResourceModel\Order\Item\Collection\Itemized as OrderItemized;

class Itemized extends OrderCorrection
{
    public function __construct(
        Config $config,
        LoggerInterface $logger,
        Store $store,
        StoreRepository $storeRepository,
        Map $orderCorrectionMap,
        OrderItemized $orderCollection
    ) {
        $this->orderCollection = $orderCollection;
        parent::__construct($config, $logger, $store, $storeRepository, $orderCorrectionMap);
    }

    protected function getFeedFields()
    {
        return [
            'PID' => Map::FIELD_PROGRAM_ID,
            'OID' => Map::FIELD_ORDER_ID,
            'ITEMID' => Map::FIELD_ITEM_ID,
            'AMOUNT' => Map::FIELD_ITEM_PRICE,
            'QUANTITY' => Map::FIELD_ITEM_QUANTITY,
            'REASON' => Map::FIELD_REASON,
        ];
    }
}
