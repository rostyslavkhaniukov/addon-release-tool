<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Commands;

use AirSlate\Releaser\Builder;
use AirSlate\Releaser\Exceptions\FileNotFoundInRepositoryException;
use AirSlate\Releaser\Processors\JsonProcessor;
use AirSlate\Releaser\Processors\XmlProcessor;
use AirSlate\Releaser\Processors\YamlProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fluffy\GithubClient\Client as GithubClient;

class CheckVersions extends Command
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

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \ReflectionException
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('<info>Adding CircleCI to addons.</info>');
        $client = new GithubClient([
            'owner' => getenv('OWNER'),
            'token' => getenv('GITHUB_OAUTH_TOKEN'),
        ]);

        $this->config = require_once './config/addons.php';
        $addons = $this->config['addons'] ?? [];

        foreach ($addons as $addon) {
            try {
                (new Builder($client, $addon))
                    ->setOutput($output)
                    ->verify(function (JsonProcessor $schema) use ($output, $addon) {
                        return $schema
                            ->take('docker/config/addon/addon.json')
                            ->isset('data.attributes.version');
                    }, function () use ($output, $addon) {
                        $output->writeln("<info>{$addon} has no version</info>");
                    });
            } catch (\Exception $e) {
                $output->writeln("<comment>{$addon} has not scheme</comment>");
            }
        }
    }
}
