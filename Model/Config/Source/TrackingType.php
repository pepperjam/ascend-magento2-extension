<?php
namespace Pepperjam\Network\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

use Pepperjam\Network\Helper\Data;

class TrackingType implements OptionSourceInterface
{
    protected $helper;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    public function toOptionArray()
    {
        return [
            [
                'value' => Data::TRACKING_BASIC,
                'label' => __('Basic'),
            ],
            [
                'value' => Data::TRACKING_ITEMIZED,
                'label' => __('Itemized'),
            ],
            [
                'value' => Data::TRACKING_DYNAMIC,
                'label' => __('Dynamic'),
            ],
        ];
    }

    public function toArray()
    {
        return [
            Data::TRACKING_BASIC => __('Basic'),
            Data::TRACKING_ITEMIZED => __('Itemized'),
            Data::TRACKING_DYNAMIC => __('Dynamic'),
        ];
    }
}
