<?php
namespace Pepperjam\Network\Setup;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

use Pepperjam\Network\Helper\Config;

class InstallData implements InstallDataInterface
{
    protected $categorySetupFactory;

    protected $config;

    public function __construct(CategorySetupFactory $categorySetupFactory, Config $config)
    {
        $this->categorySetupFactory = $categorySetupFactory;
        $this->config = $config;
    }

    // This is disabled because removing the parameter would break the interface contract
// @codingStandardsIgnoreStart
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
// @codingStandardsIgnoreEnd
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

        $setup->startSetup();

        $categorySetup->addAttribute(
            Product::ENTITY,
            'commissioning_category',
            [
                'type' => 'int',
                'label' => 'Commissioning Category',
                'input' => 'select',
                'source' => 'Pepperjam\Network\Model\Config\Source\CommissioningCategory',
                'sort_order' => 80,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => '',
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_for_promo_rules' => false,
                'required' => false,
            ]
        );

        $setup->endSetup();

        $this->config->setOrderCorrectionFeedLastRunTime(time());
    }
}
