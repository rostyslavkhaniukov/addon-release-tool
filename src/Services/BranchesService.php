<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Services;

use AirSlate\Releaser\Entities\Branch;
use AirSlate\Releaser\Entities\File;
use AirSlate\Releaser\Entities\Label;
use AirSlate\Releaser\Http\Client as HttpClient;

/**
 * @package AirSlate\Releaser\Services
 */
class BranchesService extends AbstractService
{
    /**
     * @param HttpClient $client
     */
    public function __construct(HttpClient $client)
    {
        parent::__construct($client, '');
    }

    /**
     * @param string $owner
     * @param string $repository
     * @param string $name
     * @return Branch
     */
    public function get(string $owner, string $repository, string $name): Branch
    {
        $response = $this->client->get("/repos/{$owner}/{$repository}/branches/{$name}");

        $content = \GuzzleHttp\json_decode($response->getBody(), true);

        return Branch::fromArray($content);
    }
}
