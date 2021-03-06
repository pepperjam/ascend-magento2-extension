<?php
namespace Pepperjam\Network\Cron\Feed;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Psr\Log\LoggerInterface;

use Pepperjam\Network\Cron\Feed;
use Pepperjam\Network\Helper\Config;
use Pepperjam\Network\Helper\Map\Product as ProductMap;

class Product extends Feed
{
    const FILENAME_FORMAT = '%s_product_feed.csv';

    protected $config;

    protected $productMap;

    protected $productCollection;

    protected $delimiter = "\t";

    public function __construct(
        Collection $productCollection,
        Config $config,
        LoggerInterface $logger,
        ProductMap $productMap
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->productMap = $productMap;
        $this->productCollection = $productCollection;
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
        $products = $this->productCollection
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
