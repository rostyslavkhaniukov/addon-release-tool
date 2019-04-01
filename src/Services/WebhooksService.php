<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Services;

use AirSlate\Releaser\Entities\Webhook;
use AirSlate\Releaser\Http\Client as HttpClient;
use GuzzleHttp\RequestOptions;

/**
 * Class WebhooksService
 * @package AirSlate\Releaser\Services
 */
class WebhooksService extends AbstractService
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
     * @return Webhook[]
     */
    public function all(string $repository): array
    {
        $response = $this->client->get("/repos/{$this->owner}/{$repository}/hooks");

        $content = \GuzzleHttp\json_decode($response->getBody(), true);

        return Webhook::fromCollection($content);
    }

    /**
     * @param string $repository
     * @return \AirSlate\Releaser\Entities\Release|Webhook
     */
    public function create(string $repository)
    {
        $response = $this->client->post("/repos/{$this->owner}/{$repository}/hooks", [
            RequestOptions::JSON => [
                'type' => 'web',
                'config' => [
                    'url' => 'http://localhost:8099/hook',
                ],
            ]
        ]);

        $content = \GuzzleHttp\json_decode($response->getBody(), true);

        return Webhook::fromArray($content);
    }
}
