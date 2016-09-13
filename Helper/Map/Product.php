<?php
namespace Pepperjam\Network\Helper\Map;

use \Magento\Framework\App\Helper\AbstractHelper;

use \Pepperjam\Network\Cron\Feed\Product as ProductFeed;

class Product extends AbstractHelper {
	public function get($product, $field) {
		if ($field == ProductFeed::FIELD_PRODUCT_URL) {
			return $this->getProductUrl($product);
		} else {
			return $product->getData($field);
		}
	}

	public function getProductUrl($product) {
		return $product->getUrlModel()->getUrl($product);
	}
}
