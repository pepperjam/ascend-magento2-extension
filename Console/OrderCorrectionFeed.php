<?php
namespace Pepperjam\Network\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Pepperjam\Network\Helper\Data;
use Magento\Framework\App\ObjectManager;

class OrderCorrectionFeed extends Command
{
    protected $config;

    protected $objectManager;

    /** @var \Magento\Framework\App\State **/
    protected $state;

    /** @var \Pepperjam\Network\Cron\Feed\OrderCorrection */
    protected $orderCorrectionFeed;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Pepperjam\Network\Helper\Config $config
    ) {
        $this->config = $config;
        $this->state = $state;
        $this->objectManager = ObjectManager::getInstance();

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('pepperjam:generate:orders')
            ->setDescription('Generate order correction feed file');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);

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

        if ($this->orderCorrectionFeed->enabled()) {

            $this->orderCorrectionFeed->execute();
            $output->writeln('File: '. $this->orderCorrectionFeed->getFilePath());

            $time = $date->diff(new \DateTime());
            $output->writeln('Done in: '. sprintf('%sH %sm %ss', $time->h, $time->i, $time->s));
        } else {
            $output->writeln('Order Feed or tracking is disabled');
        }

    }
}