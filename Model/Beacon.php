<?php
namespace Pepperjam\Network\Model;

use \Pepperjam\Network\Helper\Data;
use \Pepperjam\Network\Helper\Config;

abstract class Beacon {
	protected $_config;
	protected $_helper;
	protected $_order;

	public function __construct(Config $config, Data $helper) {
		$this->_config = $config;
		$this->_helper = $helper;
	}

	public function setOrder($order) {
		$this->_order = $order;
	}

	protected function _getCouponCode($params) {
		if (trim($this->_order->getCouponCode()) != '') {
			$params[$this->_couponKey] = trim($order->getCouponCode());
		}

		return $params;
	}

	public abstract function getUrl();

	protected abstract function _orderParams();
}
