<?php
namespace Pepperjam\Network\Helper;

use Magento\Config\Model\ResourceModel\Config as ConfigResource;
use Magento\Framework\App\Cache\Manager as CacheManager;
use Magento\Framework\App\Cache\Type\Config as CacheTypeConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    protected $cacheManager;

    protected $configResource;

    protected $scopeConfig;

    public function __construct(
        CacheManager $cacheManager,
        ConfigResource $configResource,
        Context $context
    ) {
        parent::__construct($context);

        $this->cacheManager = $cacheManager;
        $this->configResource = $configResource;
        $this->scopeConfig = $context->getScopeConfig();
    }

    public function isActive()
    {
        return (boolean) $this->scopeConfig->getValue('pepperjam_network/settings/active');
    }
    public function getProgramId($storeCode = null)
    {
        return $this->scopeConfig->getValue('pepperjam_network/settings/program_id', ScopeInterface::SCOPE_STORE, $storeCode);
    }
    public function getTrackingType()
    {
        return $this->scopeConfig->getValue('pepperjam_network/settings/tracking_type');
    }
    public function getTransactionType()
    {
        return $this->scopeConfig->getValue('pepperjam_network/settings/transaction_type');
    }
    public function getExportPath()
    {
        return $this->scopeConfig->getValue('pepperjam_network/feeds/export_path');
    }
    public function isAttributionEnabled()
    {
        return (boolean) $this->isActive() &&
            $this->scopeConfig->getValue('pepperjam_network/settings/attribution_enabled');
    }
    public function getSourceKeyName()
    {
        return $this->scopeConfig->getValue('pepperjam_network/settings/source_key_name');
    }
    public function isProductFeedEnabled()
    {
        return (boolean) $this->isActive() &&
            $this->scopeConfig->getValue('pepperjam_network/feeds/product_feed_enabled');
    }
    public function isOrderCorrectionFeedEnabled()
    {
        return (boolean) $this->isActive() &&
            $this->scopeConfig->getValue('pepperjam_network/feeds/order_correction_feed_enabled');
    }
    public function getProductFeedMap()
    {
        return $this->scopeConfig->getValue('pepperjam_network/product_map');
    }

    public function getValidSources()
    {
        // Magic strings, but they are not used anywhere else
        return ['eems', 'pepperjam'];
    }

    public function getBeaconBaseUrl()
    {
        if ($this->isCustomDomainEnabled()) {
            return 'https://'. $this->scopeConfig->getValue('pepperjam_network/custom_domain/url') . '/track';
        } else {
            return 'https://'. $this->scopeConfig->getValue('pepperjam_network/settings/beacon_base_url') . '/track';
        }
    }

    public function getInt()
    {
        return strtoupper($this->getTrackingType());
    }

    public function getRequiredProductFeedFields()
    {
        $pepperjamConfig = $this->config->getSection();
    }

    public function getOrderCorrectionFeedLastRunTime()
    {
        return $this->scopeConfig->getValue('pepperjam_network/feed/order_correction/last_run_time');
    }

    public function setOrderCorrectionFeedLastRunTime($time)
    {
        $this->configResource->saveConfig(
            'pepperjam_network/feed/order_correction/last_run_time',
            $time,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            0
        );
        $this->cacheManager->clean([CacheTypeConfig::TYPE_IDENTIFIER]);
    }

    public function getLookBack()
    {
        return $this->scopeConfig->getValue('pepperjam_network/settings/lookback_duration');
    }

    public function isTagEnabled()
    {
        return (bool)$this->scopeConfig->getValue('pepperjam_network/container_tag/enabled');
    }

    public function getTagIdentifier()
    {
        return $this->scopeConfig->getValue('pepperjam_network/container_tag/identifier', ScopeInterface::SCOPE_STORE);
    }

    public function getJsEndpoint()
    {
        if ($this->isCustomDomainEnabled()) {
            return '//cdn.'. $this->scopeConfig->getValue('pepperjam_network/custom_domain/url');
        } else {
            return '//'. $this->scopeConfig->getValue('pepperjam_network/container_tag/endpoint_js');
        }
    }

    public function isCustomDomainEnabled()
    {
        return (bool)$this->scopeConfig->getValue('pepperjam_network/custom_domain/enabled');
    }

    public function getBeaconProductIdMaxSize()
    {
        return (int)$this->scopeConfig->getValue('pepperjam_network/settings/beacon_product_id_max_size');
    }

    public function isCurrencySupportEnabled()
    {
        return (bool)$this->scopeConfig->getValue('pepperjam_network/settings/currency_support');
    }

    public function getPlatformIdentifier()
    {
        return $this->scopeConfig->getValue('pepperjam_network/settings/platform_id');
    }
}
