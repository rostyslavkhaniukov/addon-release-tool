<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Services;

use AirSlate\Releaser\Http\Client as HttpClient;
use GuzzleHttp\RequestOptions;

/**
 * Class CheckRunsService
 * @package AirSlate\Releaser\Services
 */
class CheckRunsService extends AbstractService
{
    /**
     * @param HttpClient $client
     * @param string $owner
     */
    public function __construct(HttpClient $client, string $owner)
    {
        parent::__construct($client, $owner);
    }

    /**
     * @param string $repository
     * @param string $ref
     * @return CheckRun[]
     */
    public function create(string $repository, string $ref): array
    {
        $response = $this->client->post("/repos/{$this->owner}/{$repository}/check-runs", [
            RequestOptions::JSON => [
                'name' => 'phpcs',
                'head_sha' => $ref,
            ],
            RequestOptions::HEADERS => [
                'Accept' => 'application/vnd.github.antiope-preview+json',
            ],
        ]);

        $content = \GuzzleHttp\json_decode($response->getBody(), true);

        var_dump($content);die;

        //return Label::fromArray($content);
    }
}
