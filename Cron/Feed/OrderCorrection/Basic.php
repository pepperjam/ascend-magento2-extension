<?php
namespace Pepperjam\Network\Cron\Feed\OrderCorrection;

use \Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use \Psr\Log\LoggerInterface;

use \Pepperjam\Network\Cron\Feed\OrderCorrection;
use \Pepperjam\Network\Helper\Config;
use \Pepperjam\Network\Helper\Map\OrderCorrection as Map;

class Basic extends OrderCorrection
{
    protected $_orderCollection;

    public function __construct(Config $config, LoggerInterface $logger, 
        Map $orderCorrectionMap, OrderCollection $orderCollection) {
        parent::__construct($config, $logger, $orderCorrectionMap);

        $this->_orderCollection = $orderCollection;
    }

    protected function _getFeedFields() {
        return [
            'PID' => Map::FIELD_PROGRAM_ID,
            'AMOUNT' => Map::FIELD_ORDER_AMOUNT,
            'OID' => Map::FIELD_ORDER_ID,
            'REASON' => Map::FIELD_REASON,
            'TYPE' => Map::FIELD_TRANSACTION_TYPE,
        ];
    }

    protected function _getItems() {
        $lastRunTime = date(self::SELECT_TIME_FORMAT, $this->_config->getOrderCorrectionFeedLastRunTime());

        $collection = $this->_orderCollection;
        $select = $collection->getSelect();
        $select
            ->joinLeft(
                ['cmo' => $collection->getTable('sales_creditmemo')],
                'main_table.entity_id = cmo.order_id',
                []
            )
            // this is far more pure SQL than should be here but I don't see a way to
            // get the proper groupings of where clauses without doing this
            ->where(
                "(main_table.original_increment_id IS NOT NULL AND main_table.created_at >= :lastRunTime " .
                "AND main_table.created_at < :startTime) OR" .
                "(cmo.created_at IS NOT NULL AND cmo.created_at >= :lastRunTime AND cmo.created_at < :startTime) OR" .
                "(main_table.state = 'canceled' AND main_table.updated_at >= :lastRunTime " .
                "AND main_table.updated_at < :startTime AND main_table.relation_child_id IS NULL)"
            );

        $collection->addBindParam(':lastRunTime', $lastRunTime)
            ->addBindParam(':startTime', $this->_startTimeFormatted);
        return $collection;
    }
}
