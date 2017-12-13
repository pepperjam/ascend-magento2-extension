<?php
namespace Pepperjam\Network\Block\Checkout\Onepage;

use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

use Pepperjam\Network\Helper\Config;
use Pepperjam\Network\Model\Attribution;
use Pepperjam\Network\Model\BeaconFactory;

class Beacon extends Template
{
    protected $attribution;
    protected $beaconFactory;
    protected $checkoutSession;
    protected $config;

    public function __construct(
        Context $context,
        Attribution $attribution,
        BeaconFactory $beaconFactory,
        Session $checkoutSession,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->attribution = $attribution;
        $this->beaconFactory = $beaconFactory;
        $this->checkoutSession = $checkoutSession;
        $this->config = $config;
    }

    public function getBeaconUrl()
    {
        $order = $this->checkoutSession->getLastRealOrder();
        $beacon = $this->beaconFactory->create($this->config->getTrackingType(), $order);

        return $beacon->getUrl();
    }

    protected function _toHtml()
    {
        if ($this->config->isActive() && !empty($this->config->getProgramId())) {
            if (!$this->config->isAttributionEnabled() || $this->attribution->isValid()) {
                return parent::_toHtml();
            }
        }

        return '';
    }
}
