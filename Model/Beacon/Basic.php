<?php
namespace Pepperjam\Network\Model\Beacon;

use Pepperjam\Network\Model\Beacon;

class Basic extends Beacon
{
    protected $couponKey = 'PROMOCODE';

    public function getUrl()
    {
        $params = $this->orderParams();
        $params = $this->getCouponCode($params);

        return $this->config->getBeaconBaseUrl() . '?' . http_build_query($params);
    }

    protected function orderParams()
    {
        return [
            'PID' => $this->config->getProgramId(),
            'OID' => $this->order->getIncrementId(),
            'AMOUNT' => $this->helper->formatMoney($this->order->getSubtotal()
                + $this->order->getDiscountAmount() + $this->order->getShippingDiscountAmount()),
            'TYPE' => $this->config->getTransactionType(),
        ];
    }
}
