<?php
namespace Pepperjam\Network\Cron\Feed;

use Magento\Catalog\Model\Product\Type as ProductType;
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

	// @codingStandardsIgnoreStart - Not storing the start time at point of construction defeats the purpose
    public function __construct(Config $config, LoggerInterface $logger, OrderCorrectionMap $orderCorrectionMap)
    {
    	// @codingStandardsIgnoreEnd
        parent::__construct($config, $logger);

        $this->orderCorrectionMap = $orderCorrectionMap;

        $this->startTime = time();
    }

    protected function buildFeedData()
    {
        $items = $this->getItems()->getItems();
        foreach ($items as $item) {
            if ($this->orderCorrectionMap->isBundle($item)) {
                $chidren = $item->getChildrenItems();
                foreach ($chidren as $i) {
                    unset($items[$i->getId()]);
                }
            }
        }
        $dataCount = count($items);
        $this->logger->info("Building feed for $dataCount items");

        return array_map([$this, 'applyMapping'], $items);
    }

    protected function applyMapping($item)
    {
        $data = [];
        $fields = $this->getFeedFields();
        foreach ($fields as $field => $attribute) {
            $data[$attribute] = $this->orderCorrectionMap->get($item, $attribute);
        }
        // Set QTY = 0 for bundle items having price = 0
        if ($this->orderCorrectionMap->isBundle($item)
            && (int)$data[OrderCorrectionMap::FIELD_ITEM_PRICE] == 0) {
            $data[OrderCorrectionMap::FIELD_ITEM_QUANTITY] = 0;
        }
        return array_values($data);
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

    protected function getItems()
    {
        $lastRunTime = date(self::SELECT_TIME_FORMAT, (int)$this->config->getOrderCorrectionFeedLastRunTime());
        $startTimeFormatted = date(self::SELECT_TIME_FORMAT, $this->startTime);

        $collection = clone $this->orderCollection;
        $collection->addFieldToFilter('o.'.OrderInterface::STORE_ID, $this->store->getId());
        $collection->addBindParam(':lastRunTime', $lastRunTime)
            ->addBindParam(':startTime', $startTimeFormatted);

        return $collection;
    }
}