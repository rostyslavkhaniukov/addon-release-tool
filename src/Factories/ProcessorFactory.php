<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Factories;

use AirSlate\Releaser\Processors\ProcessorInterface;
use Closure;
use Fluffy\GithubClient\Client;
use ReflectionFunction;

/**
 * @package AirSlate\Releaser\Factories
 */
class ProcessorFactory
{
    /**
     * @param Closure $closure
     * @param Client $client
     * @param string $owner
     * @param string $repository
     * @return ProcessorInterface
     * @throws \ReflectionException
     */
    public function make(Closure $closure, Client $client, string $owner, string $repository): ProcessorInterface
    {
        $reflection = new ReflectionFunction($closure);
        $arguments  = $reflection->getParameters();

        /** @var \ReflectionParameter $processorParameter */
        $processorParameter = reset($arguments);
        $processorClass = $processorParameter->getClass();

        /** @var ProcessorInterface $instance */
        $instance = $processorClass->newInstance($client, $owner, $repository);
        return $instance;
    }
}
