<?php
namespace Pepperjam\Network\Cron\Feed;

use \Magento\Catalog\Api\Data\ProductInterface;
use \Magento\Catalog\Model\Product\Attribute\Source\Status;
use \Magento\Catalog\Model\ResourceModel\Product\Collection;
use \Psr\Log\LoggerInterface;

use \Pepperjam\Network\Cron\Feed;
use \Pepperjam\Network\Helper\Config;
use \Pepperjam\Network\Helper\Map\Product as ProductMap;

// TODO: ignore products that don't have all required fields filled.

class Product extends Feed {
    const FILENAME_FORMAT = '%s_product_feed.csv';

    protected $_config;

    protected $_productMap;

    protected $_products;

    protected $_delimiter = "\t";

    public function __construct(Collection $products, Config $config, LoggerInterface $logger, ProductMap $productMap) {
        $this->_config = $config;
        $this->_logger = $logger;
        $this->_productMap = $productMap;

        $this->_products = $products
            ->addAttributeToSelect('*')
            ->addFieldToFilter(ProductInterface::STATUS, Status::STATUS_ENABLED)
            ->load();
    }

    protected function _applyMapping($item) {
        $data = [];
        $fields = $this->_getFeedFields();
        foreach ($fields as $field => $attribute) {
            if ($attribute != '') {
                $data[] = $this->_productMap->get($item, $field, $attribute);
            }
        }

        return $data;
    }

    protected function _enabled() {
        return $this->_config->isProductFeedEnabled();
    }

    protected function _getFeedFields() {
        return $this->_config->getProductFeedMap();
    }

    protected function _getFileName() {
        return sprintf(self::FILENAME_FORMAT, $this->_config->getProgramId());
    }

    protected function _getItems() {
        $this->_products
            ->addAttributeToSelect('*')
            ->addFieldToFilter(ProductInterface::STATUS, Status::STATUS_ENABLED);
        
        return $this->_products;
    }
}
