<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Commands\PullRequests\NewRegistration;

use AirSlate\Releaser\Builder;
use AirSlate\Releaser\Commands\AddonsCommand;
use AirSlate\Releaser\Processors\AddonSchemeProcessor;
use AirSlate\Releaser\Processors\FileProcessor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewRegistration extends AddonsCommand
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('pr:registration')
            ->setDescription('Enable new registration');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function beforeCommand(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Enable new registration in addons.</info>');
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
            ->verify(function (AddonSchemeProcessor $scheme) use ($output, $addon) {
                return $scheme
                    ->take('.circleci/config.yml');
            }, function () use ($output, $addon) {
                $output->writeln("<comment>{$addon} has no PHPStan</comment>");
            })
            ->step(function (FileProcessor $file) use ($output) {
                $output->writeln("<info>Checked</info>");
            });
    }
}
