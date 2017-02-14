<?php
namespace Pepperjam\Network\Cron;

use Magento\Framework\App\ObjectManager;

use Pepperjam\Network\Helper\Config;
use Pepperjam\Network\Helper\Data;

class OrderCorrectionFactory
{
    protected $config;

    protected $objectManager;

    protected $orderCorrectionFeed;

    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->objectManager = ObjectManager::getInstance();
    }

    public function execute()
    {
        var_dump('execute');
        try {
        switch ($this->config->getTrackingType()) {
            case Data::TRACKING_BASIC:
                $this->orderCorrectionFeed = $this->objectManager
                    ->get('\Pepperjam\Network\Cron\Feed\OrderCorrection\Basic');
                break;
            case Data::TRACKING_ITEMIZED:
                $this->orderCorrectionFeed = $this->objectManager
                    ->get('\Pepperjam\Network\Cron\Feed\OrderCorrection\Itemized');
                break;
            case Data::TRACKING_DYNAMIC:
                $this->orderCorrectionFeed = $this->objectManager
                    ->get('\Pepperjam\Network\Cron\Feed\OrderCorrection\Dynamic');
                break;
        }

        $this->orderCorrectionFeed->execute();
        } catch (\Exception $e) {
            var_dump($e);
        }
    }
}
