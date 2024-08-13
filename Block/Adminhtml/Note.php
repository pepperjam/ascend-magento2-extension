<?php
namespace Pepperjam\Network\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Pepperjam\Network\Helper\Config;

class Note extends Field
{
    public function __construct(Context $context,
                                Config $config,
                                array $data = [],
                                SecureHtmlRenderer $secureRenderer = null)
    {
        $this->config = $config;
        parent::__construct($context, $data, $secureRenderer);
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        $link = $this->config->getSignatureDocUrl();
        return 'For more information about the Pixel Signature feature, please visit <a href="'. $link. '" target="_blank">this page</a>';
    }
}