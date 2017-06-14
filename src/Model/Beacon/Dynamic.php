<?php
namespace Pepperjam\Network\Model\Beacon;

use Pepperjam\Network\Model\Beacon\Itemized;

class Dynamic extends Itemized
{
    protected $couponKey = 'COUPON';
    protected $priceKey = 'ITEM_PRICE';
    protected $quantityKey = 'QUANTITY';
    protected $skuKey = 'ITEM_ID';

    protected function orderParams()
    {
        return [
            'PROGRAM_ID' => $this->config->getProgramId(),
            'ORDER_ID' => $this->order->getIncrementId(),
            'INT' => $this->config->getInt(),
            'NEW_TO_FILE' => (int) $this->helper->isNewToFile($this->order),
        ];
    }

    protected function newItem($params, $item, $itemIndex)
    {
        $params = parent::newItem($params, $item, $itemIndex);
        $params['CATEGORY' . $itemIndex] = $this->helper->getCommissioningCategory($item);

        return $params;
    }
}
