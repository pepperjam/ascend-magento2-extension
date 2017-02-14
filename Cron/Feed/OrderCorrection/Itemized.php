<?php
namespace Pepperjam\Network\Cron\Feed\OrderCorrection;

use Psr\Log\LoggerInterface;

use Pepperjam\Network\Cron\Feed\OrderCorrection;
use Pepperjam\Network\Helper\Config;
use Pepperjam\Network\Helper\Map\OrderCorrection as Map;
use Pepperjam\Network\Model\ResourceModel\Order\Item\Collection\Itemized as OrderItemized;

class Itemized extends OrderCorrection
{
    protected $orderItemCollection;

    public function __construct (
        Config $config,
        LoggerInterface $logger,
        Map $orderCorrectionMap,
        OrderItemized $orderItemCollection
    ) {
        parent::__construct($config, $logger, $orderCorrectionMap);

        $this->orderItemCollection = $orderItemCollection;
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

    protected function getItems()
    {
        $lastRunTime = date(self::SELECT_TIME_FORMAT, $this->config->getOrderCorrectionFeedLastRunTime());

        var_dump($this->startTimeFormatted);
        var_dump($lastRunTime);

        $collection = $this->orderItemCollection;

        $collection->addBindParam(':lastRunTime', $lastRunTime)
            ->addBindParam(':startTime', $this->startTimeFormatted);

        var_dump($collection->getSelectSql(true));

        return $collection;
    }
}
