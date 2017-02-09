<?php
namespace Pepperjam\Network\Model;

use \Magento\Backend\Model\Session;
use \Magento\Framework\App\RequestInterface;

use \Pepperjam\Network\Helper\Config;

class Attribution {
    protected $_config;
    protected $_request;
    protected $_session;

    public function __construct(Config $config, RequestInterface $request, Session $session) {
        $this->_config = $config;
        $this->_request = $request;
        $this->_session = $session;
    }

    public function create() {
        // This is expected to get more involved with the most recent round of changes
        $source = $this->_request->getParam($this->_config->getSourceKeyName());
        if (in_array($source, $this->_config->getValidSources())) {
            $this->_session->setSource($source);
        }
    }

    public function isValid() {
        return in_array($this->_session->getSource(), $this->_config->getValidSources());
    }
}
