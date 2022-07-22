<?php
namespace Pepperjam\Network\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProductFeed extends Command
{
    protected $productFeed;

    public function __construct(
        \Pepperjam\Network\Cron\Feed\Product $productFeed
    ) {
        $this->productFeed = $productFeed;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('pepperjam:generate:products')
            ->setDescription('Generate products feed file');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $date = new \DateTime();
        if ($this->productFeed->enabled()) {

            $this->productFeed->execute();
            $output->writeln('File: '. $this->productFeed->getFilePath());

            $time = $date->diff(new \DateTime());
            $output->writeln('Done in: '. sprintf('%sH %sm %ss', $time->h, $time->i, $time->s));
        } else {
            $output->writeln('Product Feed or tracking is disabled');
        }

    }
}