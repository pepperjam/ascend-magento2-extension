<?php
namespace Pepperjam\Network\Model\ResourceModel\Order\Collection;

use Magento\Sales\Model\ResourceModel\Order\Collection;

class Basic extends Collection
{
    protected function _initSelect()
    {
        parent::_initSelect();

        $select = $this->getSelect();

        $select
            ->joinLeft(
                ['cmo' => $this->getTable('sales_creditmemo')],
                'main_table.entity_id = cmo.order_id',
                []
            )
            // this is far more pure SQL than should be here but I don't see a way to
            // get the proper groupings of where clauses without doing this
            ->where(
                "(main_table.original_increment_id IS NOT NULL AND main_table.created_at >= :lastRunTime " .
                "AND main_table.created_at < :startTime) OR" .
                "(cmo.created_at IS NOT NULL AND cmo.created_at >= :lastRunTime AND cmo.created_at < :startTime) OR " .
                "(main_table.state = 'canceled' AND main_table.updated_at >= :lastRunTime " .
                "AND main_table.updated_at < :startTime AND main_table.relation_child_id IS NULL)"
            );
    }
}
