<?php
namespace Pepperjam\Network\Helper\Map;

use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App\Helper\Context;

use \Pepperjam\Network\Cron\Feed\Product as ProductFeed;
use \Pepperjam\Network\Helper\Data;

class Product extends AbstractHelper {
	const ATTRIBUTE_PRODUCT_URL = 'product_url';

	const FIELD_AGE_RANGE = 'age_range';
	const FIELD_ARTIST = 'artist';
	const FIELD_ASPECT_RATIO = 'aspect_ratio';
	const FIELD_AUTHOR = 'author';
	const FIELD_BATTERY_LIFE = 'battery_life';
	const FIELD_BINDING = 'binding';
	const FIELD_BUY_URL = 'buy_url';
	const FIELD_CATEGORY_NETWORK = 'category_network';
	const FIELD_CATEGORY_PROGRAM = 'category_program';
	const FIELD_COLOR = 'color';
	const FIELD_COLOR_OUTPUT = 'color_output';
	const FIELD_CONDITION = 'condition';
	const FIELD_DESCRIPTION_LONG = 'description_long';
	const FIELD_DESCRIPTION_SHORT = 'description_short';
	const FIELD_DIRECTOR = 'director';
	const FIELD_DISCONTINUED = 'discontinued';
	const FIELD_DISPLAY_TYPE = 'display_type';
	const FIELD_EDITION = 'edition';
	const FIELD_EXPIRATION_DATE = 'expiration_date';
	const FIELD_FEATURES = 'features';
	const FIELD_FOCUS_TYPE = 'focus_type';
	const FIELD_FORMAT = 'format';
	const FIELD_FUNCTIONS = 'functions';
	const FIELD_GENRE = 'genre';
	const FIELD_HEEL_HEIGHT = 'heel_height';
	const FIELD_HEIGHT = 'height';
	const FIELD_IMAGE_THUMB_URL = 'image_thumb_url';
	const FIELD_IMAGE_URL = 'image_url';
	const FIELD_INSTALLATION = 'installation';
	const FIELD_IN_STOCK = 'in_stock';
	const FIELD_ISBN = 'isbn';
	const FIELD_KEYWORDS = 'keywords';
	const FIELD_LENGTH = 'length';
	const FIELD_LOAD_TYPE = 'load_type';
	const FIELD_LOCATION = 'location';
	const FIELD_MADE_IN = 'made_in';
	const FIELD_MANUFACTURER = 'manufacturer';
	const FIELD_MATERIAL = 'material';
	const FIELD_MEGAPIXELS = 'megapixels';
	const FIELD_MEMORY_CAPACITY = 'memory_capacity';
	const FIELD_MEMORY_CARD_SLOT = 'memory_card_slot';
	const FIELD_MEMORY_TYPE = 'memory_type';
	const FIELD_MODEL_NUMBER = 'model_number';
	const FIELD_MPN = 'mpn';
	const FIELD_NAME = 'name';
	const FIELD_OCCASION = 'occasion';
	const FIELD_OPERATING_SYSTEM = 'operating_system';
	const FIELD_OPTICAL_DRIVE = 'optical_drive';
	const FIELD_PAGES = 'pages';
	const FIELD_PAYMENT_ACCEPTED = 'payment_accepted';
	const FIELD_PAYMENT_NOTES = 'payment_notes';
	const FIELD_PLATFORM = 'platform';
	const FIELD_PRICE = 'price';
	const FIELD_PRICE_RETAIL = 'price_retail';
	const FIELD_PRICE_SALE = 'price_sale';
	const FIELD_PRICE_SHIPPING = 'price_shipping';
	const FIELD_PROCESSOR = 'processor';
	const FIELD_PUBLISHER = 'publisher';
	const FIELD_QUANTITY_IN_STOCK = 'quantity_in_stock';
	const FIELD_RATING = 'rating';
	const FIELD_RECOMMENDED_USAGE = 'recommended_usage';
	const FIELD_RESOLUTION = 'resolution';
	const FIELD_SCREEN_SIZE = 'screen_size';
	const FIELD_SHIPPING_METHOD = 'shipping_method';
	const FIELD_SHOE_SIZE = 'shoe_size';
	const FIELD_SHOE_WIDTH = 'shoe_width';
	const FIELD_SIZE = 'size';
	const FIELD_SKU = 'sku';
	const FIELD_STARING = 'staring';
	const FIELD_STYLE = 'style';
	const FIELD_TECH_SPEC_URL = 'tech_spec_url';
	const FIELD_TRACKS = 'tracks';
	const FIELD_UPC = 'upc';
	const FIELD_WEIGHT = 'weight';
	const FIELD_WIDTH = 'width';
	const FIELD_WIRELESS_INTERFACE = 'wireless_interface';
	const FIELD_YEAR = 'year';
	const FIELD_ZOOM = 'zoom';

	protected $_helper;

	public function __construct(Context $context, Data $helper) {
		parent::__construct($context);

		$this->_helper = $helper;
	}

	public function get($product, $field, $attribute) {
		if ($attribute == self::ATTRIBUTE_PRODUCT_URL) {
			$value = $this->getProductUrl($product);
		} else {
			$value = $product->getData($attribute);
		}

		return $this->_formatValue($field, $value);
	}

	public function getProductUrl($product) {
		return $product->getUrlModel()->getUrl($product);
	}

	protected function _formatValue($field, $value) {
		switch ($field) {
			case self::FIELD_ASPECT_RATIO:
				return $this->_trimToMaxLength($value, 16);
			case self::FIELD_AGE_RANGE:
			case self::FIELD_BATTERY_LIFE:
			case self::FIELD_BINDING:
			case self::FIELD_COLOR:
			case self::FIELD_DISPLAY_TYPE:
			case self::FIELD_EDITION:
			case self::FIELD_HEEL_HEIGHT:
			case self::FIELD_HEIGHT:
			case self::FIELD_LENGTH:
			case self::FIELD_LOAD_TYPE:
			case self::FIELD_MEMORY_CARD_SLOT:
			case self::FIELD_RATING:
			case self::FIELD_SCREEN_SIZE:
			case self::FIELD_SHOE_SIZE:
			case self::FIELD_SHOE_WIDTH:
			case self::FIELD_SIZE:
			case self::FIELD_WEIGHT:
			case self::FIELD_WIDTH:
			case self::FIELD_WIRELESS_INTERFACE:
			case self::FIELD_ZOOM:
				return $this->_trimToMaxLength($value, 32);
			case self::FIELD_CONDITION:
			case self::FIELD_FORMAT:
			case self::FIELD_FUNCTIONS:
			case self::FIELD_GENRE:
			case self::FIELD_INSTALLATION:
			case self::FIELD_ISBN:
			case self::FIELD_LOCATION:
			case self::FIELD_MADE_IN:
			case self::FIELD_MEMORY_CAPACITY:
			case self::FIELD_MEMORY_TYPE:
			case self::FIELD_OPTICAL_DRIVE:
			case self::FIELD_PLATFORM:
			case self::FIELD_PROCESSOR:
			case self::FIELD_RESOLUTION:
			case self::FIELD_SHIPPING_METHOD:
			case self::FIELD_STYLE:
				return $this->_trimToMaxLength($value, 64);
			case self::FIELD_ARTIST:
			case self::FIELD_AUTHOR:
			case self::FIELD_DIRECTOR:
			case self::FIELD_FEATURES:
			case self::FIELD_FOCUS_TYPE:
			case self::FIELD_MANUFACTURER:
			case self::FIELD_MATERIAL:
			case self::FIELD_MODEL_NUMBER:
			case self::FIELD_MPN:
			case self::FIELD_NAME:
			case self::FIELD_OCCASION:
			case self::FIELD_OPERATING_SYSTEM:
			case self::FIELD_PAYMENT_ACCEPTED:
			case self::FIELD_PUBLISHER:
			case self::FIELD_RECOMMENDED_USAGE:
			case self::FIELD_SKU:
			case self::FIELD_STARING:
			case self::FIELD_UPC:
				return $this->_trimToMaxLength($value, 128);
			case self::FIELD_CATEGORY_NETWORK:
			case self::FIELD_CATEGORY_PROGRAM:
			case self::FIELD_KEYWORDS:
			case self::FIELD_PAYMENT_NOTES:
				return $this->_trimToMaxLength($value, 256);
			case self::FIELD_DESCRIPTION_SHORT:
				return $this->_trimToMaxLength($value, 512);
			case self::FIELD_BUY_URL:
			case self::FIELD_DESCRIPTION_LONG:
			case self::FIELD_IMAGE_THUMB_URL:
			case self::FIELD_IMAGE_URL:
			case self::FIELD_TECH_SPEC_URL:
				return $this->_trimToMaxLength($value, 2000);
			case self::FIELD_COLOR_OUTPUT:
			case self::FIELD_DISCONTINUED:
			case self::FIELD_IN_STOCK:
				return $this->_getValueYesNo($value);
			case self::FIELD_EXPIRATION_DATE:
				return $this->_getDateValue($value);
			case self::FIELD_MEGAPIXELS:
			case self::FIELD_PRICE:
			case self::FIELD_PRICE_RETAIL:
			case self::FIELD_PRICE_SALE:
			case self::FIELD_PRICE_SHIPPING:
				return $this->_helper->formatMoney((float) $value);
			case self::FIELD_PAGES:
			case self::FIELD_QUANTITY_IN_STOCK:
			case self::FIELD_TRACKS:
			case self::FIELD_YEAR:
			default:
				return $value;
		}
	}

	protected function _getDateValue($value) {
		return date('Y-m-d', strtotime($value));	
	}

	protected function _getValueYesNo($value) {
		if ($value) {
			return 'yes';
		} else {
			return 'no';
		}
	}

	protected function _trimToMaxLength($value, $maxLength) {
		return substr(trim($value), 0, $maxLength);
	}
}
