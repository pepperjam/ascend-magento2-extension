<?php
namespace Pepperjam\Network\Model;

use Pepperjam\Network\Helper\Data;
use Pepperjam\Network\Helper\Config;

abstract class Beacon
{
    protected $config;
    protected $helper;
    protected $order;

    public function __construct (Config $config, Data $helper)
    {
        $this->config = $config;
        $this->helper = $helper;
    }

    public function setOrder($order)
    {
        $this->order = $order;
    }

    protected function getCouponCode($params)
    {
        if (trim($this->order->getCouponCode()) != '') {
            $params[$this->couponKey] = trim($order->getCouponCode());
        }

        return $params;
    }

    abstract public function getUrl();

    abstract protected function orderParams();
}
