<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Commands;

use AirSlate\Releaser\Builder;
use AirSlate\Releaser\Processors\JsonProcessor;
use AirSlate\Releaser\Services\SchemesPathsFetcher;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckVersions extends AddonsCommand
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('versions:check')
            ->setDescription('Check versions');
    }

    protected function beforeCommand(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Check if addon has versions.</info>');
    }

    /**
     * @param string $addon
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \ReflectionException
     */
    protected function step(string $addon, InputInterface $input, OutputInterface $output)
    {
        $schemesFetcher = new SchemesPathsFetcher();
        $schemes = $schemesFetcher->fetch($addon);

        foreach ($schemes as $scheme) {
            (new Builder($this->client, $addon))
                ->setOutput($output)
                ->verify(function (JsonProcessor $schema) use ($output, $addon, $scheme) {
                    return $schema
                        ->take($scheme)
                        ->isset('data.attributes.version');
                }, function () use ($output, $addon) {
                    $output->writeln("<comment>Something in repo {$addon} has no version</comment>");
                });
        }
    }
}
