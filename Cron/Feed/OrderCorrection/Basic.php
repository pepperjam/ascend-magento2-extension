<?php
namespace Pepperjam\Network\Cron\Feed\OrderCorrection;

use Psr\Log\LoggerInterface;

use Pepperjam\Network\Cron\Feed\OrderCorrection;
use Pepperjam\Network\Helper\Config;
use Pepperjam\Network\Helper\Map\OrderCorrection as Map;
use Pepperjam\Network\Model\ResourceModel\Order\Collection\Basic as OrderCollection;

class Basic extends OrderCorrection
{
    protected $orderCollection;

    public function __construct (
        Config $config,
        LoggerInterface $logger,
        Map $orderCorrectionMap,
        OrderCollection $orderCollection
    ) {
        parent::__construct($config, $logger, $orderCorrectionMap);

        $this->orderCollection = $orderCollection;
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

    protected function getItems()
    {
        $lastRunTime = date(self::SELECT_TIME_FORMAT, $this->config->getOrderCorrectionFeedLastRunTime());

        $collection = $this->orderCollection;

        $collection->addBindParam(':lastRunTime', $lastRunTime)
            ->addBindParam(':startTime', $this->startTimeFormatted);
        return $collection;
    }
}
