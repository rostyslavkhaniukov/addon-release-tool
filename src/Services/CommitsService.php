<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Services;

use AirSlate\Releaser\Entities\Label;
use AirSlate\Releaser\Http\Client as HttpClient;
use GuzzleHttp\RequestOptions;

/**
 * Class CommitsService
 * @package AirSlate\Releaser\Services
 */
class CommitsService extends AbstractService
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
     * @return Label[]
     */
    public function checkSuites(string $repository, string $ref): array
    {
        $response = $this->client->get("/repos/{$this->owner}/{$repository}/commits/{$ref}/check-suites", [
            RequestOptions::HEADERS => [
                'Accept' => 'application/vnd.github.antiope-preview+json',
            ]
        ]);

        $content = \GuzzleHttp\json_decode($response->getBody(), true);

        // return Label::fromCollection($content);
    }
}
