<?php
namespace Pepperjam\Network\Helper\Map;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

use Pepperjam\Network\Cron\Feed\Product as ProductFeed;
use Pepperjam\Network\Helper\Data;

class Product extends AbstractHelper
{
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

    protected $helper;

    public function __construct(Context $context, Data $helper)
    {
        parent::__construct($context);

        $this->helper = $helper;
    }

    public function get($product, $field, $attribute)
    {
        if ($attribute == self::ATTRIBUTE_PRODUCT_URL) {
            $value = $this->getProductUrl($product);
        } else {
            $value = $product->getData($attribute);
        }

        return $this->formatValue($field, $value);
    }

    public function getProductUrl($product)
    {
        return $product->getUrlModel()->getUrl($product);
    }

	// @codingStandardsIgnoreStart - this needs refactoring, but it's a complex amount of logic
    protected function formatValue($field, $value)
    {
        if (in_array($field, [self::FIELD_ASPECT_RATIO])) {
            return $this->trimToMaxLength($value, 16);
        } elseif (in_array(
            $field,
            [
                self::FIELD_AGE_RANGE,
                self::FIELD_BATTERY_LIFE,
                self::FIELD_BINDING,
                self::FIELD_COLOR,
                self::FIELD_DISPLAY_TYPE,
                self::FIELD_EDITION,
                self::FIELD_HEEL_HEIGHT,
                self::FIELD_HEIGHT,
                self::FIELD_LENGTH,
                self::FIELD_LOAD_TYPE,
                self::FIELD_MEMORY_CARD_SLOT,
                self::FIELD_RATING,
                self::FIELD_SCREEN_SIZE,
                self::FIELD_SHOE_SIZE,
                self::FIELD_SHOE_WIDTH,
                self::FIELD_SIZE,
                self::FIELD_WEIGHT,
                self::FIELD_WIDTH,
                self::FIELD_WIRELESS_INTERFACE,
                self::FIELD_ZOOM,
            ]
        )
        ) {
            return $this->trimToMaxLength($value, 32);
        } elseif (in_array(
            $field,
            [
                self::FIELD_CONDITION,
                self::FIELD_FORMAT,
                self::FIELD_FUNCTIONS,
                self::FIELD_GENRE,
                self::FIELD_INSTALLATION,
                self::FIELD_ISBN,
                self::FIELD_LOCATION,
                self::FIELD_MADE_IN,
                self::FIELD_MEMORY_CAPACITY,
                self::FIELD_MEMORY_TYPE,
                self::FIELD_OPTICAL_DRIVE,
                self::FIELD_PLATFORM,
                self::FIELD_PROCESSOR,
                self::FIELD_RESOLUTION,
                self::FIELD_SHIPPING_METHOD,
                self::FIELD_STYLE,
            ]
        )
        ) {
            return $this->trimToMaxLength($value, 64);
        } elseif (in_array(
            $field,
            [
                self::FIELD_ARTIST,
                self::FIELD_AUTHOR,
                self::FIELD_DIRECTOR,
                self::FIELD_FEATURES,
                self::FIELD_FOCUS_TYPE,
                self::FIELD_MANUFACTURER,
                self::FIELD_MATERIAL,
                self::FIELD_MODEL_NUMBER,
                self::FIELD_MPN,
                self::FIELD_NAME,
                self::FIELD_OCCASION,
                self::FIELD_OPERATING_SYSTEM,
                self::FIELD_PAYMENT_ACCEPTED,
                self::FIELD_PUBLISHER,
                self::FIELD_RECOMMENDED_USAGE,
                self::FIELD_SKU,
                self::FIELD_STARING,
                self::FIELD_UPC,
            ]
        )
        ) {
            return $this->trimToMaxLength($value, 128);
        } elseif (in_array(
            $field,
            [
                self::FIELD_CATEGORY_NETWORK,
                self::FIELD_CATEGORY_PROGRAM,
                self::FIELD_KEYWORDS,
                self::FIELD_PAYMENT_NOTES,
            ]
        )
        ) {
            return $this->trimToMaxLength($value, 256);
        } elseif (in_array($field, [self::FIELD_DESCRIPTION_SHORT])) {
            return $this->trimToMaxLength($value, 512);
        } elseif (in_array(
            $field,
            [
                self::FIELD_BUY_URL,
                self::FIELD_DESCRIPTION_LONG,
                self::FIELD_IMAGE_THUMB_URL,
                self::FIELD_IMAGE_URL,
                self::FIELD_TECH_SPEC_URL,
            ]
        )
        ) {
            return $this->trimToMaxLength($value, 2000);
        } elseif (in_array(
            $field,
            [
                self::FIELD_COLOR_OUTPUT,
                self::FIELD_DISCONTINUED,
                self::FIELD_IN_STOCK,
            ]
        )
        ) {
            return $this->getValueYesNo($value);
        } elseif (in_array($field, [self::FIELD_EXPIRATION_DATE])) {
            return $this->getDateValue($value);
        } elseif (in_array(
            $field,
            [
                self::FIELD_MEGAPIXELS,
                self::FIELD_PRICE,
                self::FIELD_PRICE_RETAIL,
                self::FIELD_PRICE_SALE,
                self::FIELD_PRICE_SHIPPING,
            ]
        )
        ) {
            return $this->helper->formatMoney((float) $value);
        } else {
            return $value;
        }
    }
	// @codingStandardsIgnoreEnd

    protected function getDateValue($value)
    {
        return date('Y-m-d', strtotime($value));
    }

    protected function getValueYesNo($value)
    {
        if ($value) {
            return 'yes';
        } else {
            return 'no';
        }
    }

    protected function trimToMaxLength($value, $maxLength)
    {
        return substr(trim($value), 0, $maxLength);
    }
}
