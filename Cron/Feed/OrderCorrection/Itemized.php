<?php
namespace Pepperjam\Network\Cron\Feed\OrderCorrection;

use \Magento\Sales\Model\ResourceModel\Order\Item\Collection as OrderCollection;
use \Psr\Log\LoggerInterface;

use \Pepperjam\Network\Cron\Feed\OrderCorrection;
use \Pepperjam\Network\Helper\Config;
use \Pepperjam\Network\Helper\Map\OrderCorrection as Map;

class Itemized extends OrderCorrection
{
    protected $_orderItemCollection;

    public function __construct(Config $config, LoggerInterface $logger, Map $orderCorrectionMap, OrderCollection $orderItemCollection) {
        parent::__construct($config, $logger, $orderCorrectionMap);

        $this->_orderItemCollection = $orderItemCollection;
    }

    protected function _getFeedFields() {
        return [
            'PID' => Map::FIELD_PROGRAM_ID,
            'OID' => Map::FIELD_ORDER_ID,
            'ITEMID' => Map::FIELD_ITEM_ID,
            'AMOUNT' => Map::FIELD_ITEM_PRICE,
            'QUANTITY' => Map::FIELD_ITEM_QUANTITY,
            'REASON' => Map::FIELD_REASON,
        ];
    }

    protected function _getItems() {
        $lastRunTime = date(self::SELECT_TIME_FORMAT, $this->_config->getOrderCorrectionFeedLastRunTime());

        $collection = $this->_orderItemCollection;
        $select = $collection->getSelect();
        $select
            ->joinLeft(
                ['o' => $collection->getTable('sales_order')],
                'main_table.order_id = o.entity_id',
                ['o.increment_id', 'o.original_increment_id']
            )
            ->joinLeft(
                ['cmo' => $collection->getTable('sales_creditmemo')],
                'main_table.order_id = cmo.order_id',
                []
            )
            // this is far more pure SQL than should be here but I don't see a way to
            // get the proper groupings of where clauses without doing this
            ->where(
                // get only items within the correct store scope and filter out any
                // configurable used simple products
                'NOT (main_table.product_type="simple" AND main_table.parent_item_id IS NOT NULL ' .
                'AND main_table.row_total=0)'
            )
            ->where(
                "(o.original_increment_id IS NOT NULL AND o.created_at >= :lastRunTime " .
                "AND o.created_at < :startTime) OR " .
                "(cmo.created_at IS NOT NULL AND cmo.created_at >= :lastRunTime AND " .
                "cmo.created_at < :startTime) OR " .
                "(o.state = 'canceled' AND o.updated_at >= :lastRunTime " .
                "AND o.updated_at < :startTime AND o.relation_child_id IS NULL)"
            )
            // The left joins can leave duplicate item rows
            // But the selected items will be identical, so we don't need them.
            ->distinct();

        $collection->addBindParam(':lastRunTime', $lastRunTime)
            ->addBindParam(':startTime', $this->_startTimeFormatted);

        return $collection;
    }
}
