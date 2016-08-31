<?php
namespace Pepperjam\Network\Model\Beacon;

use \Magento\Bundle\Model\Product\Price;
use \Magento\Catalog\Model\Product\Type as ProductType;

use \Pepperjam\Network\Model\Beacon;

// TODO with this and Dynamic:
// add all keys as protected fields
// so in dynamic _addItems, it can call parent::_newItem without issue
// then all it has to add is category
// Similar with orderParams

class Itemized extends Beacon {
	protected $_couponKey = 'PROMOCODE';
	protected $_skuKey = 'ITEM';

	public function getUrl() {
		$params = $this->_orderParams();
		$params = $this->_getCouponCode($params);
		$params = $this->_addItems($params);

		return $this->_config->getBeaconBaseUrl() . '?' . http_build_query($params);
	}

	protected function _orderParams() {
		return [
			'PID' => $this->_config->getProgramId(),
			'OID' => $this->_order->getIncrementId(),
			'INT' => $this->_config->getInt(),
		];
	}

	protected function _addItems($params) {
		$itemIndex = 1;

		foreach ($this->_order->getAllItems() as $item) {
			$position = $this->_getPosition($params, $item);
			if ($position) {
				$params = $this->_existingItem($params, $item, $position);
			} else {
				$params = $this->_newItem($params, $item, $itemIndex);

				$itemIndex++; // Only increment after adding a new item
			}
		}

		return $params;
	}

	protected function _getPosition($params, $item) {
		$key = array_search($item->getSku(), $params, true);

		if ($key) {
			return (int) str_replace($this->_skuKey, '', $key);
		} else {
			return false;
		}
	}

	protected function _newItem($params, $item, $itemIndex) {
		$params[$this->_skuKey . $itemIndex] = $item->getSku();
		$params['QTY' . $itemIndex] = $this->_getQuantity($item);
		$params['AMOUNT' . $itemIndex] = $this->_getPrice($item);

		return $params;
	}

	protected function _existingItem($params, $item, $itemIndex) {
		$params['QTY' . $itemIndex] += $this->_getQuantity();
		$priceKey = 'AMOUNT' . $itemIndex;
		$params[$priceKey] = $this->_helper->formatMoney($params[$priceKey] + $this->_getPrice());

		return $params;
	}

	protected function _getQuantity($item) {
		if ($item->getProduct()->canConfigure()) {
			return 0;
		} else {
			return (int) $item->getQtyOrdered();
		}
	}

	protected function _getPrice($item) {
		if ($item->getProduct()->getTypeId() === ProductType::TYPE_BUNDLE && $item->getProduct()->getPriceType() === Price::PRICE_TYPE_DYNAMIC) {
			return '0.00';
		} else {
			return $this->_helper->formatMoney($item->getPrice() - ($item->getDiscountAmount() / $item->getQtyOrdered()));
		}
	}
}
