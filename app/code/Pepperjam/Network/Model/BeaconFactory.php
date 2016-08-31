<?php
namespace Pepperjam\Network\Model;

use \Magento\Framework\App\ObjectManager;

use \Pepperjam\Network\Helper\Data;

class BeaconFactory {
	protected $_helper;

	public function __construct(Data $helper) {
		$this->_helper = $helper;
	}

	public function createBasic($order) {
		$beacon = ObjectManager::getInstance()->get('\Pepperjam\Network\Model\Beacon\Basic');
		$beacon->setOrder($order);

		return $beacon;
	}

	public function createItemized($order) {
		$beacon = ObjectManager::getInstance()->get('\Pepperjam\Network\Model\Beacon\Itemized');
		$beacon->setOrder($order);

		return $beacon;
	}

	public function createDynamic($order) {
		$beacon = ObjectManager::getInstance()->get('\Pepperjam\Network\Model\Beacon\Dynamic');
		$beacon->setOrder($order);

		return $beacon;
	}

	public function create($trackingType, $order) {
		switch ($trackingType) {
			case $this->_helper::TRACKING_BASIC:
				return $this->createBasic($order);
			case $this->_helper::TRACKING_ITEMIZED:
				return $this->createItemized($order);
			case $this->_helper::TRACKING_DYNAMIC:
				return $this->createDynamic($order);
			default:
				return false;
		}
	}
}
