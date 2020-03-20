<?php
namespace Pepperjam\Network\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Pepperjam\Network\Helper\Data;
use Magento\Framework\App\ObjectManager;
use Symfony\Component\Console\Input\InputArgument;

class OrderCorrectionFeed extends Command
{
    const INPUT_KEY_STORE_ID = 'store_id';

    protected $config;

    protected $objectManager;

    /** @var \Pepperjam\Network\Cron\Feed\OrderCorrection */
    protected $orderCorrectionFeed;

    public function __construct(
        \Pepperjam\Network\Helper\Config $config
    ) {
        $this->config = $config;
        $this->objectManager = ObjectManager::getInstance();

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('pepperjam:generate:orders')
            ->setDescription('Generate order correction feed file')
            ->setDefinition($this->getInputList());

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $date = new \DateTime();

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

        $storeId = $input->getArgument(self::INPUT_KEY_STORE_ID);
        if ($storeId) {
            $this->orderCorrectionFeed->setStore($storeId);
        }

        $result = $this->orderCorrectionFeed->execute();
        foreach ($result as $file => $count) {
            $output->writeln(sprintf('Added %s items to file: %s', $count, $file));
        }

        $time = $date->diff(new \DateTime());
        $output->writeln('Done in: '. sprintf('%sH %sm %ss', $time->h, $time->i, $time->s));
    }

    /**
     * Get list of options and arguments for the command
     *
     * @return mixed
     */
    public function getInputList()
    {
        return [
            new InputArgument(
                self::INPUT_KEY_STORE_ID,
                InputArgument::OPTIONAL,
                'Specify the store ID if you need to run only the feed for a specific store. ' .
                'When store_id is not provided, it will generate a feed file for each store'
            )
        ];
    }
}