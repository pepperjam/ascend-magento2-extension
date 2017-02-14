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
                'value' => $this->helper::TRACKING_BASIC,
                'label' => __('Basic'),
            ],
            [
                'value' => $this->helper::TRACKING_ITEMIZED,
                'label' => __('Itemized'),
            ],
            [
                'value' => $this->helper::TRACKING_DYNAMIC,
                'label' => __('Dynamic'),
            ],
        ];
    }

    public function toArray()
    {
        return [
            $this->helper::TRACKING_BASIC => __('Basic'),
            $this->helper::TRACKING_ITEMIZED => __('Itemized'),
            $this->helper::TRACKING_DYNAMIC => __('Dynamic'),
        ];
    }
}
