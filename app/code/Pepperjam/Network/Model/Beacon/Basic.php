<?php
namespace Pepperjam\Network\Model\Beacon;

use \Pepperjam\Network\Model\Beacon;

class Basic extends Beacon {
	protected $_couponKey = 'PROMOCODE';

	public function getUrl() {
		$params = $this->_orderParams();
		$params = $this->_getCouponCode($params);

		return $this->_config->getBeaconBaseUrl() . '?' . http_build_query($params);
	}

	protected function _orderParams() {
		return array(
			'PID' => $this->_config->getProgramId(),
			'OID' => $this->_order->getIncrementId(),
			'AMOUNT' => $this->_helper->formatMoney($this->_order->getSubtotal() + $this->_order->getDiscountAmount() + $this->_order->getShippingDiscountAmount()),
			'TYPE' => $this->_config->getTransactionType(),
		);
	}
}