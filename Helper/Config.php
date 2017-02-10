<?php
namespace Pepperjam\Network\Helper;

use \Magento\Framework\App\Cache\Manager as CacheManager;
use \Magento\Framework\App\Cache\Type\Config as CacheTypeConfig;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App\Helper\Context;
use \Magento\Config\Model\ResourceModel\Config as ConfigResource;

class Config extends AbstractHelper
{
    protected $_cacheManager;

    protected $_configResource;

    protected $_scopeConfig;

    public function __construct(CacheManager $cacheManager, ConfigResource $configResource, Context $context, 
        ScopeConfigInterface $scopeConfig) {
        parent::__construct($context);

        $this->_cacheManager = $cacheManager;
        $this->_configResource = $configResource;
        $this->_scopeConfig = $scopeConfig;
    }

    public function isActive()
    {
        return (boolean) $this->_scopeConfig->getValue('pepperjam_network/settings/active');
    }
    public function getProgramId()
    {
        return $this->_scopeConfig->getValue('pepperjam_network/settings/program_id');
    }
    public function getTrackingType()
    {
        return $this->_scopeConfig->getValue('pepperjam_network/settings/tracking_type');
    }
    public function getTransactionType()
    {
        return $this->_scopeConfig->getValue('pepperjam_network/settings/transaction_type');
    }
    public function getExportPath()
    {
        return $this->_scopeConfig->getValue('pepperjam_network/settings/export_path');
    }
    public function isAttributionEnabled()
    {
        return (boolean) $this->isActive() &&
            $this->_scopeConfig->getValue('pepperjam_network/settings/attribution_enabled');
    }
    public function getSourceKeyName()
    {
        return $this->_scopeConfig->getValue('pepperjam_network/settings/source_key_name');
    }
    public function isProductFeedEnabled()
    {
        return (boolean) $this->isActive() &&
            $this->_scopeConfig->getValue('pepperjam_network/settings/product_feed_enabled');
    }
    public function isOrderCorrectionFeedEnabled()
    {
        return (boolean) $this->isActive() &&
            $this->_scopeConfig->getValue('pepperjam_network/settings/order_correction_feed_enabled');
    }
    public function getProductFeedMap()
    {
        return $this->_scopeConfig->getValue('pepperjam_network/product_map');
    }

    public function getValidSources()
    {
        // Magic strings, but they are not used anywhere else
        return ['eems', 'pepperjam'];
    }

    public function getBeaconBaseUrl()
    {
        // Magic string, but it is not used anywhere else
        return 'https://t.pepperjamnetwork.com/track';
    }

    public function getInt()
    {
        return strtoupper($this->getTrackingType());
    }

    public function getRequiredProductFeedFields()
    {
        $pepperjamConfig = $this->_config->getSection();
    }

    public function getOrderCorrectionFeedLastRunTime()
    {
        return $this->_scopeConfig->getValue('pepperjam_network/feed/order_correction/last_run_time');
    }

    public function setOrderCorrectionFeedLastRunTime($time)
    {
        $this->_configResource->saveConfig('pepperjam_network/feed/order_correction/last_run_time', $time, 
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->_cacheManager->clean([CacheTypeConfig::TYPE_IDENTIFIER]);
    }
}
