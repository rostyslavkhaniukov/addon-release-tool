<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Services;

use Fluffy\GithubClient\Client as GithubClient;

/**
 * @package AirSlate\Releaser
 */
class EnvCtmplPathsFetcher
{
    /** @var GithubClient */
    private $client;

    public function __construct()
    {
        $this->client = new GithubClient([
            'owner' => getenv('OWNER'),
            'token' => getenv('GITHUB_OAUTH_TOKEN'),
        ]);
    }

    public function fetch(string $repository): \Generator
    {
        $branch = $this->client->branches()->get(getenv('OWNER'), $repository, 'develop');
        $tree = $this->client
            ->trees()
            ->get(getenv('OWNER'), $repository, $branch->commit->sha);

        foreach ($tree->getTree() as $treeLeaf) {
            if (strpos($treeLeaf['path'], '/env.ctmpl') !== false) {
                yield $treeLeaf['path'];
            }
        }
    }
}
