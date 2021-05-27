<?php
/**
 * RocketWeb
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category  RocketWeb
 * @package   RocketWeb_ShoppingFeeds
 * @copyright Copyright (c) 2016 RocketWeb (http://rocketweb.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author    Rocket Web Inc.
 */
namespace Pepperjam\Network\Block\Adminhtml;

class Domain extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     * @param \Magento\Framework\Module\ResourceInterface $moduleResource
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Remove the scope label
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _renderScopeLabel(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return '';
    }

    /**
     * List modules and their versions
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return '<input id="pepperjam_network_settings_domain_url" type="text" name="groups[settings][fields][domain_url][value]" class="input-text admin__control-text" placeholder="subdomain.yourdomain.com" value="'. $this->_scopeConfig->getValue('pepperjam_network/settings/domain_url'). '"/>
<br/><br/><ul>
<li>The host part of the uri https://pepperjam.my-domain.com should match your primary domain.</li>
<li>The sub-domain part of the uri https://pepperjam.my-domain.com will be a separate sub-domain setup for the purpose of integrating with the feature. It will need to be separate from any other sub-domain currently in use. Pepperjam is used as a placeholder in this instance.</li>
<li>Your custom domain must be provisioned in ascend before you can complete the integration</li>
<li>Your custom domain must match the exact value entered in Ascend</li>
<li>You can set up your domain in Ascend <a href="https://ascend.pepperjam.com/merchant/integration/tracking-domains" target="_blank">here</a></li>
</ul>';
    }
}