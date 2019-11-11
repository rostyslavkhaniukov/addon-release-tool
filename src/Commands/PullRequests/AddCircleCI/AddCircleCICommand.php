<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Commands\PullRequests\AddCircleCI;

use AirSlate\Releaser\Builder;
use AirSlate\Releaser\Processors\ComposerProcessor;
use AirSlate\Releaser\Processors\FileProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fluffy\GithubClient\Client as GithubClient;

class AddCircleCICommand extends Command
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('pr:circleci')
            ->setDescription('Add CircleCI to addon');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('<info>Adding CircleCI to addons.</info>');

        $client = new GithubClient([
            'owner' => getenv('OWNER'),
            'token' => getenv('GITHUB_OAUTH_TOKEN'),
        ]);

        foreach (['tags-addon'] as $addon) {
            try {
                (new Builder($client, 'airslateinc', $addon))
                    ->forTask('composer')
                    ->from('develop')
                    ->branch('test')
                    ->step(function (FileProcessor $circleci) {
                        return $circleci->createFromFile(
                            '.circleci/config.yml',
                            __DIR__ . '/config.yml'
                        );
                    })
                    ->step(function (ComposerProcessor $dependency) {
                        return $dependency
                            ->ensure('phpstan/phpstan', true);
                    })
                    ->commit('Update deps')
                    ->push()
                    ->makePR('Update deps', 'Autogenerated PR');
            } catch (\Throwable $e) {
                var_dump($e->getMessage(), $e->getFile(), $e->getLine());
            }
        }
    }
}
