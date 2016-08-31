<?php
namespace Pepperjam\Network\Model\Beacon;

use \Pepperjam\Network\Model\Beacon\Itemized;

class Dynamic extends Itemized {
	protected $_couponKey = 'COUPON';
	protected $_skuKey = 'ITEM_ID';

	protected function _orderParams() {
		return [
			'PROGRAM_ID' => $this->_config->getProgramId(),
			'ORDER_ID' => $this->_order->getIncrementId(),
			'INT' => $this->_config->getInt(),
			'NEW_TO_FILE' => (int) $this->_helper->isNewToFile($this->_order),
		];
	}

	protected function _newItem($params, $item, $itemIndex) {
		$params[$this->_skuKey . $itemIndex] = $item->getSku();
		$params['QUANTITY' . $itemIndex] = $this->_getQuantity($item);
		$params['ITEM_PRICE' . $itemIndex] = $this->_getPrice($item);
		$params['CATEGORY' . $itemIndex] = $this->_helper->getCommissioningCategory($item);

		return $params;
	}

	protected function _existingItem($params, $item, $itemIndex) {
		$params['QUANTITY' . $itemIndex] += $this->_getQuantity();
		$priceKey = 'ITEM_PRICE' . $itemIndex;
		$params[$priceKey] = $this->_helper->formatMoney($params[$priceKey] + $this->_getPrice());

		return $params;
	}
}
