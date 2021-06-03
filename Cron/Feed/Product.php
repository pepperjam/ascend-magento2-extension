<?php
namespace Pepperjam\Network\Cron\Feed;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreRepository;
use Psr\Log\LoggerInterface;

use Pepperjam\Network\Cron\Feed;
use Pepperjam\Network\Helper\Config;
use Pepperjam\Network\Helper\Map\Product as ProductMap;

class Product extends Feed
{
    const FILENAME_FORMAT = '%s_%s_product_feed.csv';

    protected $config;

    protected $productMap;

    protected $productCollection;

    protected $delimiter = "\t";

    public function __construct(
        Config $config,
        LoggerInterface $logger,
        Store $store,
        StoreRepository $storeRepository,
        Collection $productCollection,
        ProductMap $productMap
    ) {
        $this->productMap = $productMap;
        $this->productCollection = $productCollection;
        parent::__construct($config, $logger, $store, $storeRepository);
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

    public function enabled()
    {
        return $this->config->isProductFeedEnabled();
    }

    protected function getFeedFields()
    {
        return $this->config->getProductFeedMap();
    }

    protected function getFileName()
    {
        $store = $this->getStore();
        return sprintf(
            self::FILENAME_FORMAT,
            $store ? $store->getId() : 0,
            $this->config->getProgramId($store ? $store->getId() : null)
        );
    }

    protected function getItems()
    {
        $id = $this->store->getId();
        $products = clone $this->productCollection;
        $products->addAttributeToSelect('*')
            ->setStoreId($id)->addStoreFilter($id)
            ->addFieldToFilter(ProductInterface::STATUS, Status::STATUS_ENABLED)
            ->load();

        $products
            ->addAttributeToSelect('*')
            ->setStoreId($id)->addStoreFilter($id)
            ->addFieldToFilter(ProductInterface::STATUS, Status::STATUS_ENABLED);
        
        return $products;
    }

    protected function afterWrite()
    {
        return $this;
    }
}
