<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Commands;

use AirSlate\Releaser\Services\ConstantsFetcher;
use Fluffy\GithubClient\Client as GithubClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConstantsFetchCommand extends Command
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('constants-fetch')
            ->setDescription('Fetch new constants from PR');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('<info>Application ready! Build something amazing.</info>');
        $client = new GithubClient([
            'owner' => getenv('OWNER'),
            'token' => getenv('GITHUB_OAUTH_TOKEN'),
        ]);

        $pull = $client->pullRequests()->get('two-way-binding-addons', 25);
        var_dump([
            'head' => $pull->head->sha,
            'base' => $pull->base['sha']
        ]);

        $fileOld = $client->contents()->read(
            'airslateinc',
            'two-way-binding-addons',
            'docker/config/addons/slack/post-finish/addon.json',
            $pull->base['sha']
        );

        $fileNew = $client->contents()->read(
            'airslateinc',
            'two-way-binding-addons',
            'docker/config/addons/slack/post-finish/addon.json',
            $pull->head->sha
        );

        $oldC = ConstantsFetcher::fetch($fileOld->getDecoded());
        $newC = ConstantsFetcher::fetch($fileNew->getDecoded());

        var_dump(array_diff($newC, $oldC));
    }
}
