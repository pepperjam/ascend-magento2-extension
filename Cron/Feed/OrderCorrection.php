<?php
namespace Pepperjam\Network\Cron\Feed;

use Psr\Log\LoggerInterface;

use Pepperjam\Network\Cron\Feed;
use Pepperjam\Network\Helper\Config;
use Pepperjam\Network\Helper\Map\OrderCorrection as OrderCorrectionMap;

abstract class OrderCorrection extends Feed
{
    const FILENAME_FORMAT = '%s_transactions_corrected_%s.csv';
    const FILENAME_TIME_FORMAT = 'YmdHis';
    const SELECT_TIME_FORMAT = 'Y-m-d H:i:s';

    protected $orderCorrectionMap;

    protected $startTime;

    protected $startTimeFormatted;

    public function __construct (Config $config, LoggerInterface $logger, OrderCorrectionMap $orderCorrectionMap)
    {
        parent::__construct($config, $logger);

        $this->orderCorrectionMap = $orderCorrectionMap;

        $this->startTime = time();
        $this->startTimeFormatted = date(self::SELECT_TIME_FORMAT, $this->startTime);
    }

    protected function applyMapping($item)
    {
        $data = [];
        $fields = $this->getFeedFields();
        foreach ($fields as $field => $attribute) {
            $data[] = $this->orderCorrectionMap->get($item, $field, $attribute);
        }

        return $data;
    }

    protected function enabled()
    {
        return $this->config->isOrderCorrectionFeedEnabled();
    }

    protected function getFileName()
    {
        return sprintf(
            self::FILENAME_FORMAT,
            $this->config->getProgramId(),
            date(static::FILENAME_TIME_FORMAT, $this->startTime)
        );
    }

    protected function afterWrite()
    {
        $this->config->setOrderCorrectionFeedLastRunTime($this->startTime);
    }
}
