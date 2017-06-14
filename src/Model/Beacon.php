<?php
namespace Pepperjam\Network\Model;

use Pepperjam\Network\Helper\Data;
use Pepperjam\Network\Helper\Config;

use Magento\Checkout\Model\Session;
use Magento\Sales\Model\OrderFactory;

abstract class Beacon
{
    protected $checkoutSession;
    protected $config;
    protected $helper;
    protected $order;

    public function __construct(Config $config, Data $helper, OrderFactory $orderFactory, Session $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
        $this->config = $config;
        $this->helper = $helper;

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

    abstract public function getUrl();

    abstract protected function orderParams();
}
