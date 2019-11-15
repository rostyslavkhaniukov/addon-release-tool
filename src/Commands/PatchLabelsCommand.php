<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Commands;

use Fluffy\GithubClient\Client as GithubClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PatchLabelsCommand extends Command
{
    /** @var array */
    private $config;

    /** @var GithubClient */
    private $client;

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('github:patch-labels')
            ->setDescription('Patch labels in GitHub repos');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('<info>Labels patching process started.</info>');
        $this->config = require_once './config/addons.php';
        $this->client = new GithubClient([
            'owner' => getenv('OWNER'),
            'token' => getenv('GITHUB_OAUTH_TOKEN'),
        ]);

        $addons = $this->config['addons'] ?? [];
        $labels = $this->config['labels'] ?? [];

        foreach ($labels as $label) {
            $this->addLabel($label, $addons, $output);
        }
    }

    private function addLabel(array $newLabel, array $addons, OutputInterface $output)
    {
        foreach ($addons as $addon) {
            $labels = $this->client->labels()->all($addon);

            $found = false;
            foreach ($labels as $label) {
                if ($label->getName() === $newLabel['name']) {
                    $found = true;
                    if ($label->getColor() !== $newLabel['color'] ||
                        $label->getDescription() !== $newLabel['description'])
                    {
                        $output->writeln(
                            "<comment>${addon}: Label found with another color or description. Patching...</comment>"
                        );

                        $this->client->labels()->update(
                            $addon,
                            $label->getName(),
                            $newLabel['color'],
                            $newLabel['description']
                        );
                    } else {
                        $output->writeln(
                            "<info>${addon}: Labels are OK</info>"
                        );
                    }
                }
            }

            if (!$found) {
                $output->writeln(
                    "<comment>${addon}: Label not found. Creating new...</comment>"
                );

                $this->client->labels()->create(
                    $addon,
                    $newLabel['name'],
                    $newLabel['color'],
                    $newLabel['description']
                );
            }
        }
    }
}