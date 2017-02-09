<?php
namespace Pepperjam\Network\Cron;

use \Magento\Framework\App\ObjectManager;

use \Pepperjam\Network\Helper\Config;

class OrderCorrectionFactory {
    protected $_config;

    protected $_objectManager;

    protected $_orderCorrectionFeed;

    public function __construct(Config $config) {
        $this->_config = $config;

        $this->_objectManager = ObjectManager::getInstance();
    }

    public function execute() {
        switch ($this->_config->getTrackingType()) {
            case Data::TRACKING_BASIC:
                $this->_orderCorrectionFeed = $this->_objectManager
                    ->get('\Pepperjam\Network\Cron\Feed\OrderCorrection\Basic');
                break;
            case Data::TRACKING_ITEMIZED:
                $this->_orderCorrectionFeed = $this->_objectManager
                    ->get('\Pepperjam\Network\Cron\Feed\OrderCorrection\Itemized');
                break;
            case Data::TRACKING_DYNAMIC:
                $this->_orderCorrectionFeed = $this->_objectManager
                    ->get('\Pepperjam\Network\Cron\Feed\OrderCorrection\Dynamic');
                break;
        }

        $this->_orderCorrectionFeed->execute();
    }
}
