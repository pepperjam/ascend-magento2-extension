<?php
namespace Pepperjam\Network\Model;

use \Magento\Framework\App\ObjectManager;

use \Pepperjam\Network\Helper\Data;

class BeaconFactory
{
    protected $_helper;

    protected $_objectManager;

    function __construct(Data $helper)
    {
        $this->_helper = $helper;

        $this->_objectManager = ObjectManager::getInstance();
    }

    function createBasic($order)
    {
        $beacon = $this->_objectManager->get('\Pepperjam\Network\Model\Beacon\Basic');
        $beacon->setOrder($order);

        return $beacon;
    }

    function createItemized($order)
    {
        $beacon = $this->_objectManager->get('\Pepperjam\Network\Model\Beacon\Itemized');
        $beacon->setOrder($order);

        return $beacon;
    }

    function createDynamic($order)
    {
        $beacon = $this->_objectManager->get('\Pepperjam\Network\Model\Beacon\Dynamic');
        $beacon->setOrder($order);

        return $beacon;
    }

    function create($trackingType, $order)
    {
        switch ($trackingType) {
            case $this->_helper::TRACKING_BASIC:
                return $this->createBasic($order);
            case $this->_helper::TRACKING_ITEMIZED:
                return $this->createItemized($order);
            case $this->_helper::TRACKING_DYNAMIC:
                return $this->createDynamic($order);
            default:
                return false;
        }
    }
}
