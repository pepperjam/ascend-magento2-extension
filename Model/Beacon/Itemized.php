<?php
namespace Pepperjam\Network\Model\Beacon;

use \Magento\Bundle\Model\Product\Price;
use \Magento\Catalog\Model\Product\Type as ProductType;

use \Pepperjam\Network\Model\Beacon;

class Itemized extends Beacon
{
    protected $_couponKey = 'PROMOCODE';
    protected $_priceKey = 'AMOUNT';
    protected $_quantityKey = 'QTY';
    protected $_skuKey = 'ITEM';

    function getUrl()
    {
        $params = $this->_orderParams();
        $params = $this->_getCouponCode($params);
        $params = $this->_addItems($params);

        return $this->_config->getBeaconBaseUrl() . '?' . http_build_query($params);
    }

    function _orderParams()
    {
        return [
            'PID' => $this->_config->getProgramId(),
            'OID' => $this->_order->getIncrementId(),
            'INT' => $this->_config->getInt(),
        ];
    }

    function _addItems($params)
    {
        $itemIndex = 1;

        foreach ($this->_order->getAllItems() as $item) {
            $position = $this->_getPosition($params, $item);
            if ($position) {
                $params = $this->_existingItem($params, $item, $position);
            } else {
                $params = $this->_newItem($params, $item, $itemIndex);

                $itemIndex++; // Only increment after adding a new item
            }
        }

        $params = $this->_averageItemAmount($params, $itemIndex);

        return $params;
    }

    function _getPosition($params, $item)
    {
        $key = array_search($item->getSku(), $params, true);

        if ($key) {
            return (int) str_replace($this->_skuKey, '', $key);
        } else {
            return false;
        }
    }

    function _newItem($params, $item, $itemIndex)
    {
        $params[$this->_skuKey . $itemIndex] = $item->getSku();
        $params[$this->_quantityKey . $itemIndex] = $this->_getQuantity($item);
        $params[$this->_priceKey . $itemIndex] = $this->_getPrice($item);

        return $params;
    }

    function _existingItem($params, $item, $itemIndex)
    {
        $params[$this->_quantityKey . $itemIndex] += $this->_getQuantity($item);
        $priceKey = $this->_priceKey . $itemIndex;
        $params[$priceKey] = $this->_helper->formatMoney($params[$priceKey] + $this->_getPrice($item));

        return $params;
    }

    function _getQuantity($item)
    {
        if ($item->getProduct()->canConfigure()) {
            return 0;
        } else {
            return (int) $item->getQtyOrdered();
        }
    }

    function _getPrice($item)
    {
        if ($item->getProduct()->getTypeId() === ProductType::TYPE_BUNDLE 
            && $item->getProduct()->getPriceType() === Price::PRICE_TYPE_DYNAMIC) {
            return '0.00';
        } else {
            return $item->getRowTotal() - $item->getDiscountAmount();
        }
    }

    function _averageItemAmount($params, $itemIndex)
    {
        for ($i = 1; $i < $itemIndex; $i++) {
            $params[$this->_priceKey.$i] = $this->_helper
                ->formatMoney($params[$this->_priceKey.$i]/$params[$this->_quantityKey.$i]);
        }

        return $params;
    }
}
