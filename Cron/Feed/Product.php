<?php
namespace Pepperjam\Network\Cron\Feed;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Psr\Log\LoggerInterface;

use Pepperjam\Network\Cron\Feed;
use Pepperjam\Network\Helper\Config;
use Pepperjam\Network\Helper\Map\Product as ProductMap;

class Product extends Feed
{
    const FILENAME_FORMAT = '%s_product_feed.csv';

    protected $config;

    protected $productMap;

    protected $productCollectionFactory;

    protected $delimiter = "\t";

    public function __construct(
        CollectionFactory $productCollectionFactory,
        Config $config,
        LoggerInterface $logger,
        ProductMap $productMap
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->productMap = $productMap;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    protected function applyMapping($item)
    {
        $data = [];
        $fields = $this->getFeedFields();
        foreach ($fields as $field => $attribute) {
            if ($attribute != '') {
                $data[] = $this->productMap->get($item, $field, $attribute);
            }
        }

        return $data;
    }

    protected function enabled()
    {
        return $this->config->isProductFeedEnabled();
    }

    protected function getFeedFields()
    {
        return $this->config->getProductFeedMap();
    }

    protected function getFileName()
    {
        return sprintf(self::FILENAME_FORMAT, $this->config->getProgramId());
    }

    protected function getItems()
    {
        $products = $this->productCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter(ProductInterface::STATUS, Status::STATUS_ENABLED)
            ->load();

        $products
            ->addAttributeToSelect('*')
            ->addFieldToFilter(ProductInterface::STATUS, Status::STATUS_ENABLED);
        
        return $products;
    }

    protected function afterWrite()
    {
        return $this;
    }
}
