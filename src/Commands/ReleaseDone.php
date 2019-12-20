<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fluffy\GithubClient\Client as GithubClient;

class ReleaseDone extends Command
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('release:done')
            ->setDescription('Merge develop to master');
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
            $diff = $client->compare()->diff(
                getenv('OWNER'),
                $addon,
                'develop',
                'master'
            );

            if ($diff->haveDiff()) {
                $output->writeln("<comment>${addon} not yet released</comment>");
            } else {
                $output->writeln("<info>${addon} released</info>");
            }
        }
    }
}
