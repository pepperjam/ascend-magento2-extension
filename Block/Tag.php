<?php
namespace Pepperjam\Network\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Pepperjam\Network\Helper\Config;

class Tag extends Template
{

    protected $config;

    public function __construct(
        Context $context,
        Config $config,
        array $data = []
    ) {
        $this->config = $config;

        parent::__construct($context, $data);
    }

    public function getIdentifier()
    {
        return $this->config->getTagIdentifier();
    }

    public function isEnabled()
    {
        return $this->config->isTagEnabled() && !empty($this->getIdentifier());
    }

    public function getNoJsEndpoint()
    {
        return $this->config->getNoJsEndpoint();
    }

    public function getJsEndpoint()
    {
        return $this->config->getJsEndpoint();
    }
}
