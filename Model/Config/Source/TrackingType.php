<?php
namespace Pepperjam\Network\Model\Config\Source;

use \Magento\Framework\Data\OptionSourceInterface;

use \Pepperjam\Network\Helper\Data;

class TrackingType implements OptionSourceInterface
{
    protected $_helper;

    function __construct(Data $helper)
    {
        $this->_helper = $helper;
    }

    function toOptionArray()
    {
        return [
            [
                'value' => $this->_helper::TRACKING_BASIC,
                'label' => __('Basic'),
            ],
            [
                'value' => $this->_helper::TRACKING_ITEMIZED,
                'label' => __('Itemized'),
            ],
            [
                'value' => $this->_helper::TRACKING_DYNAMIC,
                'label' => __('Dynamic'),
            ],
        ];
    }

    function toArray()
    {
        return [
            $this->_helper::TRACKING_BASIC => __('Basic'),
            $this->_helper::TRACKING_ITEMIZED => __('Itemized'),
            $this->_helper::TRACKING_DYNAMIC => __('Dynamic'),
        ];
    }
}
