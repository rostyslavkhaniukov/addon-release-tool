<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Commands;

use AirSlate\Releaser\Builder;
use AirSlate\Releaser\Processors\ComposerProcessor;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckDependencies extends AddonsCommand
{
    /** @var Table */
    private $table;

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('deps:check')
            ->setDescription('Check dependencies')
            ->addArgument('package', InputArgument::REQUIRED, 'Package for check.')
            ->addArgument('operator', InputArgument::REQUIRED, 'Operator for check.')
            ->addArgument('version', InputArgument::REQUIRED, 'Version for check.');
    }

    protected function beforeCommand(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Check if addon has dependency.</info>');

        $this->table = new Table($output);
    }

    /**
     * @param string $addon
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \ReflectionException
     */
    protected function step(string $addon, InputInterface $input, OutputInterface $output)
    {
        (new Builder($this->client, $addon))
            ->setOutput($output)
            ->verify(function (ComposerProcessor $schema) use ($input, $output, $addon) {
                $lockedVersion = $schema->getLockedVersion($input->getArgument('package'));
                if ($lockedVersion === null) {
                    return false;
                }

                if (
                    version_compare(
                        trim($lockedVersion, 'v'),
                        $input->getArgument('version'),
                        $input->getArgument('operator')
                    )
                ) {
                    $this->table->addRow([
                        $addon, $lockedVersion
                    ]);
                }

                return $lockedVersion !== null;
            }, function () use ($output, $addon) {
                $this->table->addRow([$addon, 'package not installed']);
            });
    }

    protected function afterCommand(InputInterface $input, OutputInterface $output)
    {
        $this->table->render();
    }
}
