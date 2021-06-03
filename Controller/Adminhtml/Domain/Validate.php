<?php
namespace Pepperjam\Network\Controller\Adminhtml\Domain;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Validate extends Action
{
    protected $resultJsonFactory;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $return = true;
        try {
            $domain = 'https://'. $this->getRequest()->getParam('url');
            $ch = curl_init($domain. '/whatismyname');
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_TIMEOUT,100);
            curl_exec($ch);
            $retCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $retUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            curl_close($ch);
            if (!in_array($retCode, ['200', '301', '302']) || $retUrl != $domain) {
                $return = false;
            }
        } catch (\Exception $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }

        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultJsonFactory->create();
        return $result->setData(['success' => $return]);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Pepperjam_Network::config');
    }
}