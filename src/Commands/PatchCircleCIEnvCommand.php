<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Commands;

use AirSlate\Releaser\CircleCIEnvPatcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PatchCircleCIEnvCommand extends Command
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('circleci')
            ->setDescription('Create a new Laravel application')
            ->addArgument('project', InputArgument::REQUIRED, 'Which circleci project affected');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('<info>Now we try to set env variables for CircleCI project.</info>');
        $this->config = require_once './config/addons.php';
        $addons = $this->config['addons'] ?? [];
        $patcher = new CircleCIEnvPatcher();

        if ($input->getArgument('project') !== 'addons') {
            $patcher->process($input->getArgument('project'), [
                'DOCKER_USER' => getenv('DOCKER_USER'),
                'DOCKER_PASS' => getenv('DOCKER_PASS'),
                'GITHUB_ACCESS_TOKEN' => getenv('GITHUB_ACCESS_TOKEN'),
            ]);
        } else {
            foreach ($addons as $addon) {
                $patcher->process($addon, [
                    //'DOCKER_USER' => getenv('DOCKER_USER'),
                    //'DOCKER_PASS' => getenv('DOCKER_PASS'),
                    'GITHUB_ACCESS_TOKEN' => getenv('GITHUB_ACCESS_TOKEN'),
                    'GITHUB_API_TOKEN' => getenv('GITHUB_API_TOKEN'),
                ]);
            }
        }
    }
}
