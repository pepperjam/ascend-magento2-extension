<?php

namespace Pepperjam\Network\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeData implements UpgradeDataInterface
{
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.6') <= 0) {
            $configTable = $setup->getTable('core_config_data');

            $setup->getConnection()->update(
                $configTable,
                ['path' => 'pepperjam_network/container_tag/enabled'],
                ['path = ?' => 'pepperjam_network/settings/tag_enabled']
            );
            $setup->getConnection()->update(
                $configTable,
                ['path' => 'pepperjam_network/container_tag/identifier'],
                ['path = ?' => 'pepperjam_network/settings/tag_identifier']
            );


            $setup->getConnection()->update(
                $configTable,
                ['path' => 'pepperjam_network/custom_domain/enabled'],
                ['path = ?' => 'pepperjam_network/settings/domain_enabled']
            );
            $setup->getConnection()->update(
                $configTable,
                ['path' => 'pepperjam_network/custom_domain/url'],
                ['path = ?' => 'pepperjam_network/settings/domain_url']
            );

            $setup->getConnection()->update(
                $configTable,
                ['path' => 'pepperjam_network/feeds/product_feed_enabled'],
                ['path = ?' => 'pepperjam_network/settings/product_feed_enabled']
            );
            $setup->getConnection()->update(
                $configTable,
                ['path' => 'pepperjam_network/feeds/order_correction_feed_enabled'],
                ['path = ?' => 'pepperjam_network/settings/order_correction_feed_enabled']
            );
            $setup->getConnection()->update(
                $configTable,
                ['path' => 'pepperjam_network/feeds/export_path'],
                ['path = ?' => 'pepperjam_network/settings/export_path']
            );
        }
    }
}