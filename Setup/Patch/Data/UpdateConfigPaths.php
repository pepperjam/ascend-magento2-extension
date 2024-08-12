<?php
namespace Pepperjam\Network\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpdateConfigPaths implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $_moduleDataSetup;

    /**
     * AddProductAttributes constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->_moduleDataSetup = $moduleDataSetup;
    }

    public function apply()
    {
        $configTable = $this->_moduleDataSetup->getTable('core_config_data');

        $this->_moduleDataSetup->getConnection()->update(
            $configTable,
            ['path' => 'pepperjam_network/container_tag/enabled'],
            ['path = ?' => 'pepperjam_network/settings/tag_enabled']
        );
        $this->_moduleDataSetup->getConnection()->update(
            $configTable,
            ['path' => 'pepperjam_network/container_tag/identifier'],
            ['path = ?' => 'pepperjam_network/settings/tag_identifier']
        );


        $this->_moduleDataSetup->getConnection()->update(
            $configTable,
            ['path' => 'pepperjam_network/custom_domain/enabled'],
            ['path = ?' => 'pepperjam_network/settings/domain_enabled']
        );
        $this->_moduleDataSetup->getConnection()->update(
            $configTable,
            ['path' => 'pepperjam_network/custom_domain/url'],
            ['path = ?' => 'pepperjam_network/settings/domain_url']
        );

        $this->_moduleDataSetup->getConnection()->update(
            $configTable,
            ['path' => 'pepperjam_network/feeds/product_feed_enabled'],
            ['path = ?' => 'pepperjam_network/settings/product_feed_enabled']
        );
        $this->_moduleDataSetup->getConnection()->update(
            $configTable,
            ['path' => 'pepperjam_network/feeds/order_correction_feed_enabled'],
            ['path = ?' => 'pepperjam_network/settings/order_correction_feed_enabled']
        );
        $this->_moduleDataSetup->getConnection()->update(
            $configTable,
            ['path' => 'pepperjam_network/feeds/export_path'],
            ['path = ?' => 'pepperjam_network/settings/export_path']
        );
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return string
     */
    public static function getVersion()
    {
        return '1.0.6';
    }
}