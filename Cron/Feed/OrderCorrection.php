<?php
namespace Pepperjam\Network\Cron\Feed;

use \Psr\Log\LoggerInterface;

use \Pepperjam\Network\Cron\Feed;
use \Pepperjam\Network\Helper\Config;
use \Pepperjam\Network\Helper\Map\OrderCorrection as OrderCorrectionMap;

abstract class OrderCorrection extends Feed {
	const FILENAME_FORMAT = '%s_transactions_corrected_%s.csv';
	const FILENAME_TIME_FORMAT = 'YmdHis';
	const SELECT_TIME_FORMAT = 'Y-m-d H:i:s';

	protected $_config;

	protected $_logger;

	protected $_orderCorrectionMap;

	protected $_startTime;

	protected $_startTimeFormatted;

	public function __construct(Config $config, LoggerInterface $logger, OrderCorrectionMap $orderCorrectionMap) {
		$this->_config = $config;
		$this->_logger = $logger;
		$this->_orderCorrectionMap = $orderCorrectionMap;

		$this->_startTime = time();
		$this->_startTimeFormatted = date(self::SELECT_TIME_FORMAT, $this->_startTime);
	}

	protected function _applyMapping($item) {
		$data = [];
		$fields = $this->_getFeedFields();
		foreach ($fields as $field => $attribute) {
			$data[] = $this->_orderCorrectionMap->get($item, $field, $attribute);
		}

		return $data;
	}

	protected function _enabled() {
		return $this->_config->isOrderCorrectionFeedEnabled();
	}

	protected function _getFileName() {
		return sprintf(self::FILENAME_FORMAT, $this->_config->getProgramId(), 
			date(static::FILENAME_TIME_FORMAT, $this->_startTime));
	}

	protected function _afterWrite() {
		$this->_config->setOrderCorrectionFeedLastRunTime($this->_startTime);
	}
}
