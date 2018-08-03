<?php
namespace Pepperjam\Network\Helper;

use Magento\Framework\App\Helper\AbstractHelper as MagentoAbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\App\Request\Http;

class LinkHelper extends MagentoAbstractHelper
{
    const LOOKBACK_DEFAULT = 60 * 60 * 24 * 60; // 60 days
    const CONNECTOR_COOKIE_NAME = 'utm_campaign';
    const CLICK_ID_NAME = 'clickId';
    const CLICK_DATE_NAME = 'clickDate';

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

    protected $utm_campaign;

    /**
     * @param Context                                    $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param CookieManagerInterface                     $cookieManager
     * @param CookieMetadataFactory                      $cookieMetadataFactory
     * @param SessionManagerInterface                    $sessionManager
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager,
        Http $request
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionManager = $sessionManager;
        $this->request = $request;
    }

    /**
     * Get data from cookie
     *
     * @return string
     */
    public function get()
    {
        $value = $this->cookieManager->getCookie($this->getCookieName());
        if ($final_value = json_decode($value, true)) {
            return $final_value;
        } else {
            return false;
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
            ->setDuration($duration ? $duration : $this->getLookBack())
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

        return void;
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

        if (! $cookie_data = $this->get()) {
            $cookie_data = [];
        }

        if (!$value) {
            return false;
        }

        // Add current value
        $cookie_data[] = [
        static::CLICK_DATE_NAME => time(),
        static::CLICK_ID_NAME => $value
        ];

        // Now dedupe data and remove expired entries
        $existing_clickIds = [];
        foreach ($cookie_data as $entry) {
            if (!is_array($entry)) {
                continue;
            }
            if (in_array($existing_clickIds, $entry[static::CLICK_ID_NAME])) {
                continue;
            }
            if ($entry[static::CLICK_DATE_NAME] + $this->getCookieLifetime() <= time()) {
                $final_cookie_data[] = $entry;
                $existing_clickIds[] = $entry[static::CLICK_ID_NAME];
            }
        }

        return $this->set($final_cookie_data, $duration);
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
     * Used to get the current LookBack
     *
     * @return int
     */
    public function getLookBack()
    {
        return static::LOOKBACK_DEFAULT;
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
        if ($this->utm_campaign = $this->request->getParam($this->getCookieName())) {
            $this->update($this->utm_campaign, $this->getLookBack());
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
        foreach ($this->get() as $click) {
            if ($click[static::CLICK_ID_NAME]) {
                $click_id_string .= $click[static::CLICK_ID_NAME] . ",";
            }
        }

        return trim($click_id_string, ",");
    }
}
