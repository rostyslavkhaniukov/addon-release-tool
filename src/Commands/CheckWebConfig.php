<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Commands;

use AirSlate\Releaser\Builder;
use AirSlate\Releaser\Exceptions\FileNotFoundInRepositoryException;
use AirSlate\Releaser\Processors\ComposerProcessor;
use AirSlate\Releaser\Processors\FileProcessor;
use AirSlate\Releaser\Processors\JsonProcessor;
use AirSlate\Releaser\Services\SchemesPathsFetcher;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckWebConfig extends AddonsCommand
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('webconfig:check')
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
            ->verify(function (FileProcessor $file) use ($output, $addon) {
                try {
                    $file->take('public/web.config');

                    $output->writeln("<comment>{$addon} has file web.config</comment>");
                } catch (FileNotFoundInRepositoryException $exception) {
                }

                return true;
            }, function () use ($output, $addon) {
                $output->writeln("<comment>Something in repo {$addon} has no version</comment>");
            });
    }
}
