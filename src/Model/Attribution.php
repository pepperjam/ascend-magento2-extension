<?php
namespace Pepperjam\Network\Model;

use Magento\Backend\Model\Session;
use Magento\Framework\App\RequestInterface;

use Pepperjam\Network\Helper\Config;

class Attribution
{
    protected $config;
    protected $request;
    protected $session;

    public function __construct(Config $config, RequestInterface $request, Session $session)
    {
        $this->config = $config;
        $this->request = $request;
        $this->session = $session;
    }

    public function create()
    {
        // This is expected to get more involved with the most recent round of changes
        $source = $this->request->getParam($this->config->getSourceKeyName());
        if (in_array($source, $this->config->getValidSources())) {
            $this->session->setSource($source);
        }
    }

    public function isValid()
    {
        return in_array($this->session->getSource(), $this->config->getValidSources());
    }
}
