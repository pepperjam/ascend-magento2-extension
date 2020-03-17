<?php
namespace Pepperjam\Network\Cron;

use Magento\App\Dir;
use Psr\Log\LoggerInterface;

use Pepperjam\Network\Helper\Config;

abstract class Feed
{
    protected $config;

    protected $logger;

    protected $delimiter = ',';

    protected $enclosure = '"';

    public function __construct(Config $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    public function execute()
    {
        if ($this->enabled()) {
            $this->writeFile($this->buildFeedData());
            $this->afterWrite();
        }
    }

    protected function buildFeedData()
    {
        $data = $this->getItems();
        $dataCount = $data->count();

        $this->logger->info("Building feed for $dataCount items");

        return array_map([$this, 'applyMapping'], $data->getItems());
    }

    protected function writeFile($feedData)
    {
        if (empty($feedData)) {
            return $this;
        }

        $tmpFile = fopen('php://temp', 'r+');
        fputcsv($tmpFile, $this->getHeaders(), $this->delimiter, $this->enclosure);
        foreach ($feedData as $row) {
            fputcsv($tmpFile, $row, $this->delimiter, $this->enclosure);
        }
        rewind($tmpFile);

        $targetPath = $this->getFilePath();
        $this->checkAndCreateFolder(dirname($targetPath));

        file_put_contents($targetPath, stream_get_contents($tmpFile));

        fclose($tmpFile);

        return $this;
    }

    abstract protected function getItems();

    abstract protected function applyMapping($item);

    protected function getHeaders()
    {
        $headers = [];

        foreach ($this->getFeedFields() as $key => $field) {
            if ($field !== null) {
                $headers[] = $key;
            }
        }

        return $headers;
    }

    protected function getFields()
    {
        $fields = [];

        foreach ($this->getFeedFields() as $key => $field) {
            if ($field !== null) {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    abstract protected function getFeedFields();

    public function getFilePath()
    {
        return $this->normalizePath(
            BP,
            $this->config->getExportPath(),
            $this->getFileName()
        );
    }

    protected function normalizePath()
    {
        $paths = implode(DIRECTORY_SEPARATOR, func_get_args());
        // Retain a single leading slash; otherwise remove all leading, trailing
        // and duplicate slashes.
        return ((substr($paths, 0, 1) === DIRECTORY_SEPARATOR) ? DIRECTORY_SEPARATOR : '') .
            implode(DIRECTORY_SEPARATOR, array_filter(explode(DIRECTORY_SEPARATOR, $paths)));
    }

    abstract protected function getFileName();

    protected function checkAndCreateFolder($dirPath)
    {
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }
        return $this;
    }

    abstract protected function afterWrite();
}
