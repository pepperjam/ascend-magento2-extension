<?php
namespace Pepperjam\Network\Model\Config\Source;

use \Magento\Framework\Data\OptionSourceInterface;

use \Pepperjam\Network\Helper\Data;

class TransactionType implements OptionSourceInterface {
	protected $_helper;

	public function __construct(Data $helper) {
		$this->_helper = $helper;
	}

	public function toOptionArray() {
		return [
			[
				'value' => $this->_helper::TRANSACTION_LEAD,
				'label' => __('Lead'),
			],
			[
				'value' => $this->_helper::TRANSACTION_SALE,
				'label' => __('Sale'),
			],
		];
	}

	public function toArray() {
		return [
			$this->_helper::TRANSACTION_LEAD => __('Lead'),
			$this->_helper::TRANSACTION_SALE => __('Sale'),
		];
	}
}
