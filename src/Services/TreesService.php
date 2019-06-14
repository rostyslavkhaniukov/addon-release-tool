<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Services;

use AirSlate\Releaser\Entities\Ref;
use AirSlate\Releaser\Http\Client as HttpClient;
use GuzzleHttp\RequestOptions;

/**
 * @package AirSlate\Releaser\Services
 */
class TreesService extends AbstractService
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
     * @param array $tree
     * @return Ref
     */
    public function createTree(string $owner, string $repository, array $tree): Ref
    {
        $response = $this->client->post("/repos/{$owner}/{$repository}/git/trees", [
            RequestOptions::JSON => $tree
        ]);

        $content = \GuzzleHttp\json_decode($response->getBody(), true);

        var_dump($content);die;
        return Ref::fromArray($content);
    }
}
