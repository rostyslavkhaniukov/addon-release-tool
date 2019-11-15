<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Commands\PullRequests\EnablePHPCS;

use AirSlate\Releaser\Builder;
use AirSlate\Releaser\Exceptions\FileNotFoundInRepositoryException;
use AirSlate\Releaser\Processors\XmlProcessor;
use AirSlate\Releaser\Processors\YamlProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fluffy\GithubClient\Client as GithubClient;

class EnablePHPCS extends Command
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('pr:phpcs')
            ->setDescription('Enable phpcs in addon');
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
                    ->verify(function (XmlProcessor $xml) use ($output, $addon) {
                        return $xml
                            ->take('phpcs.xml.dist')
                            ->checkPSR12();
                    }, function () use ($output, $addon) {
                        $output->writeln("<comment>{$addon} has PSR-2</comment>");
                    })->notify("already PSR-12");
            } catch (\Exception $e) {
                $output->writeln("<comment>{$addon} has not PHPCS</comment>");
            }
        }
    }

    public function getCheckPHPCSCallback(): callable
    {
        return function (array $content) {
            $steps = $content['jobs']['test']['steps'];
            $phpcsStep = array_filter($steps, function ($step) {
                if (is_array($step)) {
                    $name = $step['run']['name'] ?? '';
                    if ($name === 'PHPCS') {
                        return true;
                    }
                }

                return false;
            });

            return count($phpcsStep) === 1;
        };
    }
}
