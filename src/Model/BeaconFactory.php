<?php
namespace Pepperjam\Network\Model;

use Magento\Framework\App\ObjectManager;

use Pepperjam\Network\Helper\Data;

class BeaconFactory
{
    protected $helper;

    protected $objectManager;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;

        $this->objectManager = ObjectManager::getInstance();
    }

    public function createBasic($order)
    {
        $beacon = $this->objectManager->get('\Pepperjam\Network\Model\Beacon\Basic');
        $beacon->setOrder($order);

        return $beacon;
    }

    public function createItemized($order)
    {
        $beacon = $this->objectManager->get('\Pepperjam\Network\Model\Beacon\Itemized');
        $beacon->setOrder($order);

        return $beacon;
    }

    public function createDynamic($order)
    {
        $beacon = $this->objectManager->get('\Pepperjam\Network\Model\Beacon\Dynamic');
        $beacon->setOrder($order);

        return $beacon;
    }

    public function create($trackingType, $order)
    {
        switch ($trackingType) {
        case Data::TRACKING_BASIC:
            return $this->createBasic($order);
        case Data::TRACKING_ITEMIZED:
            return $this->createItemized($order);
        case Data::TRACKING_DYNAMIC:
            return $this->createDynamic($order);
        default:
            return false;
        }
    }
}
