<?php
namespace Pepperjam\Network\Model\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

use Pepperjam\Network\Helper\Config;
use Pepperjam\Network\Model\Attribution;

class Tracking implements ObserverInterface
{
    protected $attribution;

    protected $config;

    public function __construct(Attribution $attribution, Config $config)
    {
        $this->attribution = $attribution;
        $this->config = $config;
    }
	// @codingStandardsIgnoreStart - removing the $observer parameter would violate the interface contract
    public function execute(Observer $observer)
    {
	    // @codingStandardsIgnoreEnd
        if ($this->config->isActive() && $this->config->isAttributionEnabled()) {
            $this->attribution->create();
        }
    }
}
