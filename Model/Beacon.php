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
    protected $orderFactory;

    const CAMPAIGN_KEY = 'CLICK_ID';

    public function __construct(
        Config $config,
        Data $helper,
        OrderFactory $orderFactory,
        Session $checkoutSession,
        LinkHelper $link_helper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->config = $config;
        $this->helper = $helper;
        $this->link_helper = $link_helper;
        $this->orderFactory = $orderFactory;
    }

    public function setOrder($order)
    {
        $this->order = $order;
    }

    protected function getCouponCode($params)
    {
        $lastOrderId = $this->checkoutSession->getLastRealOrderId();
        $this->order = $this->orderFactory->create()->loadByIncrementId($lastOrderId);
        $couponCode = $this->order->getCouponCode();
        if ($couponCode && trim($couponCode) != '') {
            $params[$this->couponKey] = trim($couponCode);
        }

        return $params;
    }

    protected function addCampaign($params)
    {
        if ($this->campaign = $this->link_helper->getClickIds()) {
            $params[static::CAMPAIGN_KEY] = trim($this->campaign);
        }

        return $params;
    }

    protected function addPlatform($params)
    {
        $platform_id = $this->config->getPlatformIdentifier();
        if ($platform_id) {
            $params['PLATFORM'] = $platform_id;
        }
        return $params;
    }

    protected function addSignature($params)
    {
        $enabled = $this->config->isSignatureEnabled();
        $secret = $this->config->getSignaturePrivateKey();
        if ($enabled and !empty($secret)) {
            $orderId = $this->order->getIncrementId();
            $signature = hash_hmac('sha256', $orderId, $secret);
            $params['SIGNATURE'] = $signature;
        }
        return $params;
    }

    abstract public function getUrl();

    abstract protected function orderParams();
}
