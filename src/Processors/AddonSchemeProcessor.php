<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Processors;

use AirSlate\Releaser\Services\SchemesPathsFetcher;
use Fluffy\GithubClient\Client;

class AddonSchemeProcessor extends JsonProcessor
{
    /** @var SchemesPathsFetcher */
    private $schemesFetcher;

    /**
     * @param Client $client
     * @param string $owner
     * @param string $repository
     */
    public function __construct(Client $client, string $owner, string $repository, string $sha)
    {
        parent::__construct($client, $owner, $repository, $sha);
        
        $this->schemesFetcher = new SchemesPathsFetcher($client);
    }

    public function takeAll()
    {
        $schemes = $this->schemesFetcher->fetch(
            $this->owner,
            $this->repository,
            'develop',
            'addon.json'
        );
        foreach ($schemes as $scheme) {
            $this->take($scheme);
        }

        return $this;
    }
}
