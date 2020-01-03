<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Commands;

use AirSlate\Releaser\Exceptions\NothingToCommitException;
use Fluffy\GithubClient\Client as GithubClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AddonsCommand extends Command
{
    /** @var array */
    private $config;

    /** @var GithubClient */
    protected $client;

    public function __construct()
    {
        parent::__construct();

        $this->config = require './config/addons.php';
        $this->client = new GithubClient([
            'owner' => getenv('OWNER'),
            'token' => getenv('GITHUB_OAUTH_TOKEN'),
        ]);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \ReflectionException
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->beforeCommand($input, $output);
        $addons = $this->config['addons'] ?? [];
        foreach ($addons as $addon) {
            try {
                $this->step($addon, $input, $output);
            } catch (NothingToCommitException $exception) {
                $this->nothingToCommit($addon);
            }
        }
    }

    protected function beforeCommand(InputInterface $input, OutputInterface $output)
    {
    }

    protected function step(string $addon, InputInterface $input, OutputInterface $output)
    {
    }

    protected function nothingToCommit(string $addon, InputInterface $input, OutputInterface $output)
    {
    }
}