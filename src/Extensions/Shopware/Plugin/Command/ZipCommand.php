<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Plugin\Command;

use ShopwareCli\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Zip a plugin
 */
class ZipCommand extends BaseCommand
{
    /**
     * @var string
     */
    protected $zipDir;

    public function doZip($plugin, $params)
    {
        $this->container->get('zip_service')->zip($plugin, $this->getTempDir(), $this->getZipDir(), $params['branch'], $params['useHttp']);
    }

    protected function configure(): void
    {
        $this
            ->setName('plugin:zip:vcs')
            ->setDescription('Creates a installable plugin zip in the current directory from VCS')
            ->addArgument(
                'names',
                InputArgument::IS_ARRAY,
                'Name of the plugin to install'
            )
            ->addOption(
                'small',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will yell in uppercase letters'
            )
            ->addOption(
                'useHttp',
                null,
                InputOption::VALUE_NONE,
                'Checkout the repo via HTTP'
            )
            ->addOption(
                'branch',
                '-b',
                InputOption::VALUE_OPTIONAL,
                'Checkout the given branch'
            );
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->zipDir = (string) \getcwd();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $names = $input->getArgument('names');
        $small = $input->getOption('small');
        $useHttp = $input->getOption('useHttp');
        $branch = $input->getOption('branch');

        $this->container->get('io_service')->cls();

        $this->container->get('plugin_column_renderer')->setSmall($small);
        $interactionManager = $this->container->get('plugin_operation_manager');

        $params = ['output' => $output, 'branch' => $branch, 'useHttp' => $useHttp];

        if (!empty($names)) {
            $interactionManager->searchAndOperate($names, [$this, 'doZip'], $params);

            return Command::SUCCESS;
        }

        $interactionManager->operationLoop([$this, 'doZip'], $params);

        return Command::SUCCESS;
    }

    protected function getTempDir(): string
    {
        $tempDirectory = \sys_get_temp_dir();
        $tempDirectory .= '/plugin-inst-' . \uniqid('', true);
        if (!\is_dir($tempDirectory) && !\mkdir($tempDirectory, 0777, true) && !\is_dir($tempDirectory)) {
            throw new \RuntimeException(\sprintf('Directory "%s" was not created', $tempDirectory));
        }

        $this->container->get('utilities')->changeDir($tempDirectory);

        return $tempDirectory;
    }

    protected function getZipDir(): string
    {
        return $this->zipDir;
    }
}
