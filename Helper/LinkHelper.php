<?php
namespace Pepperjam\Network\Helper;

use Magento\Framework\App\Helper\AbstractHelper as MagentoAbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Magento\Framework\App\Request\Http;
use Pepperjam\Network\Helper\Config;

class LinkHelper extends MagentoAbstractHelper
{
    const COOKIE_LIFETIME = 31536000; // 1 year
    const CONNECTOR_COOKIE_NAME = 'clickId';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $sessionManager;

    /**
     * @var Magento\Framework\App\Request\Http
     */
    protected $request;
    protected $clickId;
    protected $config;

    /**
     * LinkHelper constructor.
     *
     * @param Context                                    $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param PhpCookieManager                           $cookieManager
     * @param CookieMetadataFactory                      $cookieMetadataFactory
     * @param SessionManagerInterface                    $sessionManager
     * @param Http                                       $request
     * @param \Pepperjam\Network\Helper\Config           $config
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        PhpCookieManager $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager,
        Http $request,
        Config $config
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionManager = $sessionManager;
        $this->request = $request;
        $this->config = $config;
    }

    /**
     * Get current cookie value
     *
     * @return array|mixed
     */
    public function get()
    {
        $value = $this->cookieManager->getCookie($this->getCookieName());
        if ($value && $final_value = json_decode($value, true)) {
            return $final_value;
        } else {
            return [];
        }
    }

    /**
     * Set the current cookie value
     *
     * @param mixed $value
     * @param mixed $duration
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function set($value, $duration = null)
    {
        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration($duration ? $duration : $this->getCookieLifetime())
            ->setPath($this->sessionManager->getCookiePath())
            ->setDomain($this->sessionManager->getCookieDomain());
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $this->cookieManager->setPublicCookie(
            $this->getCookieName(),
            $value,
            $metadata
        );

        return $value;
    }

    /**
     * Delete current cookie
     *
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function deleteCookie()
    {
        return $this->cookieManager->deleteCookie($this->getCookieName());
    }

    /**
     * Update the current cookie values
     *
     * @param null $value
     * @param null $duration
     *
     * @return bool|mixed
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function update($value = null, $duration = null)
    {
        if (!$value) {
            return false;
        }
        if (!$cookie_data = $this->get()) {
            $cookie_data = [];
        } else {
            $this->deleteCookie();
        }
        // Add current value
        $cookie_data[$value] = time();

        return $this->set($cookie_data, $duration);
    }

    /**
     * Pull the Lookback value from configuration
     *
     * @return mixed
     */
    public function getLookback()
    {
        return $this->config->getLookBack();
    }

    /**
     * Removes expired entries from Cookie Data
     *
     * @param array $cookie_data
     *
     * @return array
     */
    public function purgeExpired(array $cookie_data = [])
    {
        $new_cookie_data = [];
        foreach ($cookie_data as $click_id => $timestamp) {
            if ($timestamp + $this->getLookback() <= time()) {
                $new_cookie_data[$click_id] = $timestamp;
            }
        }
        return $new_cookie_data;
    }

    /**
     * Used to get cookie's name by which data can be set or get
     *
     * @return string
     */
    public function getCookieName()
    {
        return static::CONNECTOR_COOKIE_NAME;
    }

    /**
     * Used to get the current Cookie Lifetime
     *
     * @return int
     */
    public function getCookieLifetime()
    {
        return static::COOKIE_LIFETIME;
    }

    /**
     * Read URL and store variables
     *
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function readUrl()
    {
        if ($this->clickId = $this->request->getParam($this->getCookieName())) {
            $this->update($this->clickId, $this->getCookieLifetime());
        }

        return $this;
    }

    /**
     * Gathers the click Ids
     *
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function getClickIds()
    {
        $this->update();
        $click_id_string = '';
        $cookie_values = $this->purgeExpired($this->get());
        foreach ($cookie_values as $clickId => $timestamp) {
            $click_id_string .= $clickId . ",";
        }

        return trim($click_id_string, ",");
    }
}
