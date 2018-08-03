<?php
namespace Pepperjam\Network\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Pepperjam\Network\Helper\LinkHelper;

class LinkProcessor extends Template
{

    protected $helper;

    public function __construct(
        Context $context,
        array $data = [],
        LinkHelper $helper
    ) {
        $this->helper = $helper;

        parent::__construct($context, $data);
    }

    public function readUrl()
    {
        $this->helper->readUrl();
    }
}
