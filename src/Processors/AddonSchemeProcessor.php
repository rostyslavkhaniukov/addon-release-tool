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
    public function __construct(Client $client, string $owner, string $repository)
    {
        parent::__construct($client, $owner, $repository);
        
        $this->schemesFetcher = new SchemesPathsFetcher();
    }

    public function map()
    {
        $schemes = $this->schemesFetcher->fetch($this->repository);
        foreach ($schemes as $scheme) {
            $this->take($scheme);
        }
    }
}
