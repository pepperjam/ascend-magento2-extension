<?php
namespace Pepperjam\Network\Model\Config\Source;

use \Magento\Catalog\Model\CategoryFactory;
use \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use \Magento\Store\Model\StoreManagerInterface;

class CommissioningCategory extends AbstractSource
{
    const CATEGORY_LOWEST_LEVEL = 1;

    protected $_categoryFactory;

    public function __construct(CategoryFactory $categoryFactory, StoreManagerInterface $storeManager)
    {
        $this->_categoryFactory = $categoryFactory;
        $this->_storeManager = $storeManager;
    }

    public function getAllOptions()
    {
        $categoryOptions = [
            [
                'value' => '',
                'label' => ' ',
            ],
        ];

        // For reference see: \Magento\Catalog\Helper\Category::getStoreCategories
        $rootCategoryId = $this->_storeManager->getStore()->getRootCategoryId();
        $rootCategory = $this->_categoryFactory->create();

        $children = $rootCategory->getCategories($rootCategoryId, self::CATEGORY_LOWEST_LEVEL, true, true, true);

        $this->_addChildren($categoryOptions, $children, 0);

        return $categoryOptions;
    }

    protected function _addChildren(&$options, $categories, $level)
    {

        if (empty($categories)) {
            return;
        }

        foreach ($categories as $category) {
            $options[] = [
                'value' => $category->getId(),
                'label' => str_repeat('-', $level) . $category->getName()
            ];

            $subcategories = $category->getChildrenCategories();
            $this->_addChildren($options, $subcategories, $level+1);
        }
    }
}
