<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Services;

use Fluffy\GithubClient\Client as GithubClient;
use Generator;

/**
 * @package AirSlate\Releaser\Services
 */
class SchemesPathsFetcher
{
    /** @var GithubClient */
    private $client;

    /**
     * @param GithubClient $client
     */
    public function __construct(GithubClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $owner
     * @param string $repository
     * @param string $branchName
     * @param string $fileName
     * @return Generator
     */
    public function fetch(
        string $owner,
        string $repository,
        string $branchName,
        string $fileName
    ): Generator {
        $branch = $this->client->branches()->get($owner, $repository, $branchName);
        $tree = $this->client
            ->trees()
            ->get($owner, $repository, $branch->commit->sha);

        foreach ($tree->getTree() as $treeLeaf) {
            if (strpos($treeLeaf['path'], '/' . $fileName) !== false) {
                yield $treeLeaf['path'];
            }
        }
    }
}
