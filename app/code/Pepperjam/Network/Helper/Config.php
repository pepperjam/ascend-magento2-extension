<?php
namespace Pepperjam\Network\Helper;

use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App\Helper\Context;

class Config extends AbstractHelper {
	protected $_scopeConfig;

	public function __construct(Context $context, ScopeConfigInterface $scopeConfig) {
		parent::__construct($context);

		$this->_scopeConfig = $scopeConfig;
	}

	public function isActive() {
		return (boolean) $this->_scopeConfig->getValue('pepperjam_network/settings/active');
	}
	public function getProgramId() {
		return $this->_scopeConfig->getValue('pepperjam_network/settings/program_id');
	}
	public function getTrackingType() {
		return $this->_scopeConfig->getValue('pepperjam_network/settings/tracking_type');
	}
	public function getTransactionType() {
		return $this->_scopeConfig->getValue('pepperjam_network/settings/transaction_type');
	}
	public function getExportPath() {
		return $this->_scopeConfig->getValue('pepperjam_network/settings/export_path');
	}
	public function isAttributionEnabled() {
		return (boolean) $this->isActive() && $this->_scopeConfig->getValue('pepperjam_network/settings/attribution_enabled');
	}
	public function getSourceKeyName() {
		return $this->_scopeConfig->getValue('pepperjam_network/settings/source_key_name');
	}
	public function isProductFeedEnabled() {
		return (boolean) $this->isActive() && $this->_scopeConfig->getValue('pepperjam_network/settings/product_feed_enabled');
	}
	public function isOrderCorrectionFeedEnabled() {
		return (boolean) $this->isActive() && $this->_scopeConfig->getValue('pepperjam_network/settings/order_correction_feed_enabled');
	}
	public function getProductFeedMap() {
		return $this->_scopeConfig->getValue('pepperjam_network/product_map');
	}

	public function getValidSources() {
		// Magic strings, but they are not used anywhere else
		return array('eems', 'pepperjam');
	}

	public function getBeaconBaseUrl() {
		// Magic string, but it is not used anywhere else
		return 'https://t.pepperjamnetwork.com/track';
	}

	public function getInt() {
		return strtoupper($this->getTrackingType());
	}
}
