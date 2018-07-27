<?php
namespace Pepperjam\Network\Model;

use Pepperjam\Network\Helper\Data;
use Pepperjam\Network\Helper\Config;
use Pepperjam\Network\Helper\LinkHelper;

use Magento\Checkout\Model\Session;
use Magento\Sales\Model\OrderFactory;

abstract class Beacon
{
    protected $checkoutSession;
    protected $config;
    protected $helper;
    protected $order;
	protected $campaign;
	protected $link_helper;

	const CAMPAIGN_KEY = 'CLICK_ID';

    public function __construct(Config $config, Data $helper, OrderFactory $orderFactory, Session $checkoutSession, LinkHelper $link_helper)
    {
        $this->checkoutSession = $checkoutSession;
        $this->config = $config;
        $this->helper = $helper;
        $this->link_helper = $link_helper;

        $lastOrderId = $checkoutSession->getLastRealOrderId();
        $this->order = $orderFactory->create()->loadByIncrementId($lastOrderId);
    }

    public function setOrder($order)
    {
        $this->order = $order;
    }

    protected function getCouponCode($params)
    {
        if (trim($this->order->getCouponCode()) != '') {
            $params[$this->couponKey] = trim($this->order->getCouponCode());
        }

        return $params;
    }

    protected function addCampaign($params)
    {
	    if ($this->campaign = $this->link_helper->get()) {
		    $params[static::CAMPAIGN_KEY] = trim($this->campaign);
	    }

	    return $params;
    }

    abstract public function getUrl();

    abstract protected function orderParams();
}
