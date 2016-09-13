<?php
namespace Pepperjam\Network\Model\Config\Source;

use \Magento\Framework\Data\OptionSourceInterface;

use \Pepperjam\Network\Helper\Data;

class TrackingType implements OptionSourceInterface {
	protected $_helper;

	public function __construct(Data $helper) {
		$this->_helper = $helper;
	}

	public function toOptionArray() {
		return array(
			array(
				'value' => $this->_helper::TRACKING_BASIC,
				'label' => __('Basic'),
			),
			array(
				'value' => $this->_helper::TRACKING_ITEMIZED,
				'label' => __('Itemized'),
			),
			array(
				'value' => $this->_helper::TRACKING_DYNAMIC,
				'label' => __('Dynamic'),
			),
		);
	}

	public function toArray() {
		return array(
			$this->_helper::TRACKING_BASIC => __('Basic'),
			$this->_helper::TRACKING_ITEMIZED => __('Itemized'),
			$this->_helper::TRACKING_DYNAMIC => __('Dynamic'),
		);
	}
}