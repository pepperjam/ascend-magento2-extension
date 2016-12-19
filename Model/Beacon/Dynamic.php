<?php
namespace Pepperjam\Network\Model\Beacon;

use \Pepperjam\Network\Model\Beacon\Itemized;

class Dynamic extends Itemized {
	protected $_couponKey = 'COUPON';
	protected $_priceKey = 'ITEM_PRICE';
	protected $_quantityKey = 'QUANTITY';
	protected $_skuKey = 'ITEM_ID';

	protected function _orderParams() {
		return array(
			'PROGRAM_ID' => $this->_config->getProgramId(),
			'ORDER_ID' => $this->_order->getIncrementId(),
			'INT' => $this->_config->getInt(),
			'NEW_TO_FILE' => (int) $this->_helper->isNewToFile($this->_order),
		);
	}

	protected function _newItem($params, $item, $itemIndex) {
		$params = parent::_newItem($params, $item, $itemIndex);
		$params['CATEGORY' . $itemIndex] = $this->_helper->getCommissioningCategory($item);

		return $params;
	}
}
