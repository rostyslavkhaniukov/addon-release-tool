<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Commands\PullRequests\FixReg;

use AirSlate\Releaser\Builder;
use AirSlate\Releaser\Commands\AddonsCommand;
use AirSlate\Releaser\Processors\FileProcessor;
use AirSlate\Releaser\Services\EnvCtmplPathsFetcher;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixRegCommand extends AddonsCommand
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('fix:reg')
            ->setDescription('Fix registration');
    }

    protected function beforeCommand(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Fix registration</info>');
    }

    /**
     * @param string $addon
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \ReflectionException
     */
    protected function step(string $addon, InputInterface $input, OutputInterface $output)
    {
        $templatesFetcher = new EnvCtmplPathsFetcher();
        $templates = $templatesFetcher->fetch($addon);

        $builder = (new Builder($this->client, $addon))
            ->setOutput($output)
            ->from('develop')
            ->forTask('AAD-1165');

        foreach ($templates as $template) {
            $builder->step(function (FileProcessor $ctmpl) use ($output, $addon, $template) {
                return $ctmpl
                    ->take($template)
                    ->regexReplace(
                        '/key \"SERVICE_KV_PATH\/data\/(.*)client-id\"/',
                        'keyOrDefault "SERVICE_KV_PATH/data${1}/client-id" ""'
                    )
                    ->regexReplace(
                        '/key \"SERVICE_KV_PATH\/data\/(.*)client-secret\"/',
                        'keyOrDefault "SERVICE_KV_PATH/data${1}/client-secret" ""'
                    );
            });
        }

        $builder
            ->branch('fix-registration')
            ->commit('Fix registration')
            ->push()
            ->makePR('Fix registration', 'Autogenerated PR');
    }
}
