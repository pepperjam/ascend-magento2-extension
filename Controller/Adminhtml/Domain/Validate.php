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
        $return = ['success' => true, 'message' => ''];
        try {
            $domain = rtrim($this->getRequest()->getParam('url'),"/");
            $regex = '/^(?!:\/\/)(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}(?:\.[a-zA-Z]{2,})?$/';
            if (preg_match($regex, $domain)) {
                $ch = curl_init('https://' . $domain . '/whatismyname');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 100);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $resp = curl_exec($ch);
                $retCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if (!in_array($retCode, ['200', '301', '302']) || trim(trim($resp)) != $domain) {
                    $return = ['success' => false,
                        'message' => "Your custom domain has not been properly provisioned in Ascend.
                                      Log in <a href=\"https://ascend.pepperjam.com/merchant/integration/tracking-domains\">here</a>
                                      to complete the provisioning"];
                }
            } else {
                $return = ['success' => false, 'message' => "Not a valid URL"];
            }
        } catch (\Exception $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }

        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultJsonFactory->create();
        return $result->setData($return);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Pepperjam_Network::config');
    }
}