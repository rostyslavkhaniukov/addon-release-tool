<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Services;

use AirSlate\Releaser\EmptyBuilder;
use AirSlate\Releaser\Factories\ProcessorFactory;
use Closure;
use Fluffy\GithubClient\Client as GithubClient;

class VerifierService
{
    /** @var GithubClient */
    private $client;

    /** @var string */
    private $repository;

    /** @var string */
    private $branchSha;

    /**
     * @param GithubClient $client
     * @param string $repository
     * @param string $branchSha
     */
    public function __construct(GithubClient $client, string $repository)
    {
        $this->client = $client;
        $this->repository = $repository;
    }

    /**
     * @param string $branchSha
     */
    public function setBranchSha(string $branchSha): void
    {
        $this->branchSha = $branchSha;
    }

    /**
     * @param Closure $closure
     * @return $this|EmptyBuilder
     * @throws \ReflectionException
     */
    public function verify(Closure $closure, ?callable $failCallback = null): bool
    {
        $factory = new ProcessorFactory();
        $processor = $factory->make(
            $closure,
            $this->client,
            $this->client->getOwner(),
            $this->repository,
            $this->branchSha
        );
        $process = $closure($processor);

        if (!$process) {
            if ($failCallback !== null) {
                $failCallback();
            }
            return false;
        }

        return true;
    }
}
