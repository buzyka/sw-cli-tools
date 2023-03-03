<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\AutoUpdate\Command;

use Humbug\SelfUpdate\Updater;
use ShopwareCli\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RollbackCommand extends BaseCommand
{
    /**
     * @var Updater
     */
    private $updater;

    public function __construct(Updater $updater)
    {
        $this->updater = $updater;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('rollback');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $result = $this->updater->rollback();
            if (!$result) {
                $output->writeln('Rollback failed!');

                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            $output->writeln('Unable to rollback');

            return Command::FAILURE;
        }

        $output->writeln('Rollback successful');

        return Command::SUCCESS;
    }
}
