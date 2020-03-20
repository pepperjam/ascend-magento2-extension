<?php
namespace Pepperjam\Network\Cron\Feed\OrderCorrection;

use Magento\Store\Model\Store;
use Magento\Store\Model\StoreRepository;
use Psr\Log\LoggerInterface;

use Pepperjam\Network\Cron\Feed\OrderCorrection;
use Pepperjam\Network\Helper\Config;
use Pepperjam\Network\Helper\Map\OrderCorrection as Map;
use Pepperjam\Network\Model\ResourceModel\Order\Collection\Basic as OrderCollection;

class Basic extends OrderCorrection
{
    public function __construct(
        Config $config,
        LoggerInterface $logger,
        Store $store,
        StoreRepository $storeRepository,
        Map $orderCorrectionMap,
        OrderCollection $orderCollection
    ) {
        $this->orderCollection = $orderCollection;
        parent::__construct($config, $logger, $store, $storeRepository, $orderCorrectionMap);
    }

    protected function getFeedFields()
    {
        return [
            'PID' => Map::FIELD_PROGRAM_ID,
            'AMOUNT' => Map::FIELD_ORDER_AMOUNT,
            'OID' => Map::FIELD_ORDER_ID,
            'REASON' => Map::FIELD_REASON,
            'TYPE' => Map::FIELD_TRANSACTION_TYPE,
        ];
    }
}
