<?php
namespace Pepperjam\Network\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class ProductFeed extends Command
{
    const INPUT_KEY_STORE_ID = 'store_id';

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
            ->setDescription('Generate products feed file')
            ->setDefinition($this->getInputList());

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->productFeed->enabled()) {
            $output->writeln('Products feed is disabled!');
            return;
        }

        $date = new \DateTime();
        $storeId = $input->getArgument(self::INPUT_KEY_STORE_ID);
        if ($storeId) {
            $this->productFeed->setStore($storeId);
        }

        $result = $this->productFeed->execute();

        foreach ($result as $file => $count) {
            $output->writeln(sprintf('Added %s items to file: %s', $count, $file));
        }

        $time = $date->diff(new \DateTime());
        $output->writeln('Done in: ' . sprintf('%sH %sm %ss', $time->h, $time->i, $time->s));
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