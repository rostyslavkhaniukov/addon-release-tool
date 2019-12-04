<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Commands\PullRequests\EnablePHPMD;

use AirSlate\Releaser\Builder;
use AirSlate\Releaser\Exceptions\FileNotFoundInRepositoryException;
use AirSlate\Releaser\Processors\XmlProcessor;
use AirSlate\Releaser\Processors\YamlProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fluffy\GithubClient\Client as GithubClient;

class EnablePHPMD extends Command
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('pr:phpmd')
            ->setDescription('Enable phpmd in addon');
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
            (new Builder($client, $addon))
                ->setOutput($output)
                ->verify(function (YamlProcessor $config) use ($output, $addon) {
                    return $config
                        ->take('.circleci/config.yml')
                        ->withCallback($this->getCheckPHPMDCallback());
                }, function () use ($output, $addon) {
                    $output->writeln("<comment>{$addon} has no PHPStan</comment>");
                });
        }
    }

    public function getCheckPHPMDCallback(): callable
    {
        return function (array $content) {
            $steps = $content['jobs']['test']['steps'];
            $phpcsStep = array_filter($steps, function ($step) {
                if (is_array($step)) {
                    $name = $step['run']['name'] ?? '';
                    if ($name === 'PHPStan') {
                        return true;
                    }
                }

                return false;
            });

            return count($phpcsStep) === 1;
        };
    }
}
