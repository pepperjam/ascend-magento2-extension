<?php
namespace Pepperjam\Network\Model\ResourceModel\Order\Item\Collection;

use Magento\Sales\Model\ResourceModel\Order\Item\Collection ;

class Itemized extends Collection
{
    protected function _initSelect()
    {
        parent::_initSelect();

        $select = $this->getSelect();

        $select->joinLeft(
            ['o' => $this->getTable('sales_order')],
            'main_table.order_id = o.entity_id',
            ['o.increment_id', 'o.original_increment_id']
        )
            ->joinLeft(
                ['cmo' => $this->getTable('sales_creditmemo')],
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
    }
}
