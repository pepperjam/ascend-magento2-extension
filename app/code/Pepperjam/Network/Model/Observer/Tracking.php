<?php
namespace Pepperjam\Network\Model\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;

use \Pepperjam\Network\Helper\Config;
use \Pepperjam\Network\Model\Attribution;

class Tracking implements ObserverInterface {
	protected $_attribution;
	
	protected $_config;

	public function __construct(Attribution $attribution, Config $config) {
		$this->_attribution = $attribution;
		$this->_config = $config;
	}

	public function execute(Observer $observer) {
		if ($this->_config->isActive() && $this->_config->isAttributionEnabled()) {
			$attribution->create();
		}
	}
}