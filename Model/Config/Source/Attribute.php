<?php
namespace Pepperjam\Network\Model\Config\Source;

use \Magento\Eav\Model\Config;
use \Magento\Framework\Data\OptionSourceInterface;

use \Pepperjam\Network\Helper\Map\Product;

class Attribute implements OptionSourceInterface
{
    protected $_eavConfig;

    function __construct(Config $eavConfig)
    {
        $this->_eavConfig = $eavConfig;
    }

    function toOptionArray()
    {
        // Start with an empty option if you'd like to not include a field
        $options = [
            [
                'value' => '',
                'label' => '',
            ],
            [
                'value' => Product::ATTRIBUTE_PRODUCT_URL,
                'label' => __('Product URL')
            ],
        ];

        $codes = $this->_eavConfig->getEntityAttributeCodes('catalog_product');
        foreach ($codes as $code) {
            $attribute = $this->_eavConfig->getAttribute('catalog_product', $code);
            $options[] = [
                'value' => $code,
                'label' => $attribute->getFrontendLabel(),
            ];
        }

        return $options;
    }

    function toArray()
    {
        // Start with an empty option if you'd like to not include a field
        $options = ['' => ''];

        $codes = $this->_eavConfig->getEntityAttributeCodes('catalog_product');
        foreach ($codes as $code) {
            $attribute = $this->_eavConfig->getAttribute('catalog_product', $code);
            $options[] = [$code => $attribute->getFrontendLabel()];
        }

        return $options;
    }
}
