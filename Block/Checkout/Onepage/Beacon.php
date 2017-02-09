<?php
namespace Pepperjam\Network\Block\Checkout\Onepage;

use \Magento\Checkout\Model\Session as CheckoutSession;
use \Magento\Framework\View\Element\Template;
use \Magento\Framework\View\Element\Template\Context;

use \Pepperjam\Network\Helper\Config;
use \Pepperjam\Network\Model\Attribution;
use \Pepperjam\Network\Model\BeaconFactory;

class Beacon extends Template {
    protected $_attribution;
    protected $_beaconFactory;
    protected $_checkoutSession;
    protected $_config;

    public function __construct(
        Context $context,
        Attribution $attribution,
        BeaconFactory $beaconFactory,
        CheckoutSession $checkoutSession,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->_attribution = $attribution;
        $this->_beaconFactory = $beaconFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_config = $config;
    }

    public function getBeaconUrl() {
        $order = $this->_checkoutSession->getLastRealOrder();
        $beacon = $this->_beaconFactory->create($this->_config->getTrackingType(), $order);

        return $beacon->getUrl();
    }

    public function _toHtml() {
        if ($this->_config->isActive()) {
            if (!$this->_config->isAttributionEnabled() || $this->_attribution->isValid()) {
                return parent::_toHtml();
            }
        }

        return '';
    }
}
