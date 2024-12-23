<?php
namespace Pepperjam\Network\Model\Config\Source;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Store\Model\StoreManagerInterface;

class CommissioningCategory extends AbstractSource
{
    const CATEGORY_LOWEST_LEVEL = 1;

    protected $categoryFactory;

    protected $storeManager;

    public function __construct(CategoryFactory $categoryFactory, StoreManagerInterface $storeManager)
    {
        $this->categoryFactory = $categoryFactory;
        $this->storeManager = $storeManager;
    }

    public function getAllOptions()
    {
        $categoryOptions = [
            [
                'value' => '',
                'label' => ' ',
            ],
        ];

// phpcs:disable Squiz.PHP.CommentedOutCode.Found
        // For reference see: \Magento\Catalog\Helper\Category::getStoreCategories
// phpcs:enable
        $rootCategoryId = $this->storeManager->getStore()->getRootCategoryId();
        $rootCategory = $this->categoryFactory->create();

        $children = $rootCategory->getCategories($rootCategoryId, self::CATEGORY_LOWEST_LEVEL, true, true, true);

        $this->addChildren($categoryOptions, $children, 0);

        return $categoryOptions;
    }

    protected function addChildren(&$options, $categories, $level)
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
            $this->addChildren($options, $subcategories, $level+1);
        }
    }
}
