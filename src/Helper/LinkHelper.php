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
	const COOKIE_LIFETIME = 172800; // 2 days
	const CONNECTOR_COOKIE_NAME = 'utm_campaign';

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
	* @param Context $context
	* @param \Magento\Store\Model\StoreManagerInterface $storeManager
	* @param CookieManagerInterface $cookieManager
	* @param CookieMetadataFactory $cookieMetadataFactory
	* @param SessionManagerInterface $sessionManager
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

		return $value;
	}

	/**
	* Set data to cookie
	*
	* @param string|array $value
	* @param int $duration
	*
	* @return void
	*/
	public function set($value, $duration = null)
	{
		$metadata = $this->cookieMetadataFactory
		->createPublicCookieMetadata()
		->setDuration($duration ? $duration : static::COOKIE_LIFETIME)
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
	 * Used to get cookie's lifetime
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
	 */
	public function readUrl()
	{
		if (! $this->utm_campaign = $this->get()) {
			$this->set($this->request->getParam($this->getCookieName()),$this->getCookieLifetime());
		}

		return $this;
	}
}