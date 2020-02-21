<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Commands;

use AirSlate\Releaser\Builder;
use AirSlate\Releaser\Processors\ComposerProcessor;
use AirSlate\Releaser\Processors\JsonProcessor;
use AirSlate\Releaser\Services\SchemesPathsFetcher;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckDependencies extends AddonsCommand
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('deps:check')
            ->setDescription('Check dependencies');
    }

    protected function beforeCommand(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Check if addon has dependency.</info>');
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
            ->verify(function (ComposerProcessor $schema) use ($output, $addon) {
                $lockedVersion = $schema->getLockedVersion('pdffiller/airslate-addon-sdk');

                if (version_compare(trim($lockedVersion, 'v'), '8.2.0', '<')) {
                    $output->writeln("<comment>{$addon} has {$lockedVersion}</comment>");
                }

                return $lockedVersion !== null;
            }, function () use ($output, $addon) {
                $output->writeln("<comment>Something in repo {$addon} has no version</comment>");
            });
    }
}
