<?php
namespace Pepperjam\Network\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProductFeed extends Command
{
    protected $productFeed;

    public function __construct(
        \Pepperjam\Network\Cron\Feed\Product $productFeed,
        $name = null
    ) {
        $this->productFeed = $productFeed;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('pepperjam:generate:products')
            ->setDescription('Generate products feed file');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->productFeed->execute();
        $output->writeln('File: '. $this->productFeed->getFilePath());
    }
}