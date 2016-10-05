<?php
namespace Pepperjam\Network\Cron;

use \Magento\App\Dir;
use \Psr\Log\LoggerInterface;

use \Pepperjam\Network\Helper\Config;

abstract class Feed {
	protected $_config;

	protected $_dir;

	protected $_logger;

	protected $_delimiter = ',';

	protected $_enclosure = '"';

	public function construct(Config $config, Dir $dir, LoggerInterface $logger) {
		$this->_config = $config;
		$this->_dir = $dir;
		$this->_logger = $logger;
	}

	public function execute() {
		if ($this->_enabled()) {
			$this->_writeFile($this->_buildFeedData());
			$this->_afterWrite();
		}
	}

	protected function _buildFeedData() {
		$data = $this->_getItems();
		$dataCount = $data->count();

		$this->_logger->info("Building feed for $dataCount items");

		return array_map(array($this, '_applyMapping'), $data->getItems());
	}

	protected function _writeFile($feedData) {
		if (empty($feedData)) {
			return $this;
		}

		$tmpFile = fopen('php://temp', 'r+');
		fputcsv($tmpFile, $this->_getHeaders(), $this->_delimiter, $this->_enclosure);
		foreach ($feedData as $row) {
			fputcsv($tmpFile, $row, $this->_delimiter, $this->_enclosure);
		}
		rewind($tmpFile);

		$targetPath = $this->_getFilePath();
		$this->_checkAndCreateFolder(dirname($targetPath));

		file_put_contents($targetPath, stream_get_contents($tmpFile));

		fclose($tmpFile);

		return $this;
	}

	protected abstract function _getItems();

	protected function _applyMapping($item) {
		$fields = array();
	}

	protected function _getHeaders() {
		$headers = array();

		foreach ($this->_getFeedFields() as $key => $field) {
			if (!is_null($field)) {
				$headers[] = $key;
			}
		}

		return $headers;
	}

	protected function _getFields() {
		$fields = array();

		foreach ($this->_getFeedFields() as $key => $field) {
			if (!is_null($field)) {
				$fields[] = $field;
			}
		}

		return $fields;
	}

	protected abstract function _getFeedFields();

	protected function _getFilePath() {
		return $this->_normalizePath(
			BP,
			$this->_config->getExportPath(),
			$this->_getFileName()
		);
	}

	protected function _normalizePath() {
		$paths = implode(DIRECTORY_SEPARATOR, func_get_args());
		// Retain a single leading slash; otherwise remove all leading, trailing
		// and duplicate slashes.
		return ((substr($paths, 0, 1) === DIRECTORY_SEPARATOR) ? DIRECTORY_SEPARATOR : '') .
			implode(DIRECTORY_SEPARATOR, array_filter(explode(DIRECTORY_SEPARATOR, $paths)));
	}

	protected abstract function _getFileName();

	protected function _checkAndCreateFolder($dirPath)
	{
		if (!file_exists($dirPath)) {
			mkdir($dirPath, 0777, true);
		}
		return $this;
	}

	protected function _afterWrite() {
		// Hook for OrderCorrection (and future feeds) to update runTime
	}
}
