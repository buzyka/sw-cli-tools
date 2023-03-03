<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\RunCli\Command;

use ShopwareCli\Command\BaseCommand;
use ShopwareCli\Services\IoService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunCliCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $shopwarePath = $input->getOption('shopwarePath');
        $arguments = \implode(' ', $input->getArgument('sw-command'));

        /** @var IoService $ioService */
        $ioService = $this->container->get('io_service');
        $shopwarePath = $this->getValidShopwarePath($shopwarePath, $ioService);

        \system("{$shopwarePath}/bin/console {$arguments}");
    }

    /**
     * @param string $shopwarePath
     */
    public function getValidShopwarePath($shopwarePath, IoService $ioService): string
    {
        if (!$shopwarePath) {
            $shopwarePath = \realpath(\getcwd());
        }

        do {
            if ($this->container->get('utilities')->isShopwareInstallation($shopwarePath)) {
                return $shopwarePath;
            }
        } while (($shopwarePath = \dirname($shopwarePath)) && $shopwarePath != '/');

        return $ioService->askAndValidate(
            'Path to your Shopware installation: ',
            [$this->container->get('utilities'), 'validateShopwarePath']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('run')
            ->setDescription('Run shopware console commands from shopware subdirectories.')
            ->addOption(
                'shopwarePath',
                null,
                InputOption::VALUE_OPTIONAL,
                'Your shopware path.',
                ''
            )
            ->addArgument(
                'sw-command',
                InputArgument::IS_ARRAY,
                'arguments for your shopare command'
            )
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> command allows you to trigger shopware cli commands from any subdirectory.
EOF
            );
    }
}
