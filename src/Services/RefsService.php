<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Services;

use AirSlate\Releaser\Entities\Ref;
use AirSlate\Releaser\Http\Client as HttpClient;
use GuzzleHttp\RequestOptions;

/**
 * @package AirSlate\Releaser\Services
 */
class RefsService extends AbstractService
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
     * @param string $ref
     * @param string $sha
     * @return Ref
     */
    public function createRef(string $owner, string $repository, string $ref, string $sha): Ref
    {
        $response = $this->client->post("/repos/{$owner}/{$repository}/git/refs", [
            RequestOptions::JSON => [
                'ref' => $ref,
                'sha' => $sha,
            ]
        ]);

        $content = \GuzzleHttp\json_decode($response->getBody(), true);

        return Ref::fromArray($content);
    }
}
