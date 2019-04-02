<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Services;

use AirSlate\Releaser\Entities\Label;
use AirSlate\Releaser\Entities\Webhook;
use AirSlate\Releaser\Http\Client as HttpClient;
use GuzzleHttp\RequestOptions;

/**
 * Class LabelsService
 * @package AirSlate\Releaser\Services
 */
class LabelsService extends AbstractService
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
     * @return Label[]
     */
    public function all(string $repository): array
    {
        $response = $this->client->get("/repos/{$this->owner}/{$repository}/labels", [
            RequestOptions::HEADERS => [
                'Accept' => 'application/vnd.github.symmetra-preview+json',
            ],
        ]);

        $content = \GuzzleHttp\json_decode($response->getBody(), true);

        return Label::fromCollection($content);
    }

    /**
     * @param string $repository
     * @param string $name
     * @param string $color
     * @param string $description
     * @return Label
     */
    public function create(string $repository, string $name, string $color, string $description)
    {
        $response = $this->client->post("/repos/{$this->owner}/{$repository}/labels", [
            RequestOptions::JSON => [
                'name' => $name,
                'color' => $color,
                'description' => $description,
            ],
            RequestOptions::HEADERS => [
                'Accept' => 'application/vnd.github.symmetra-preview+json',
            ],
        ]);

        $content = \GuzzleHttp\json_decode($response->getBody(), true);

        return Label::fromArray($content);
    }

    /**
     * @param string $repository
     * @param $name
     * @param $color
     * @param $description
     * @return \AirSlate\Releaser\Entities\Release|Label
     */
    public function update(string $repository, $name, $color, $description)
    {
        $response = $this->client->patch("/repos/{$this->owner}/{$repository}/labels/{$name}", [
            RequestOptions::JSON => [
                'name' => $name,
                'color' => $color,
                'description' => $description,
            ],
            RequestOptions::HEADERS => [
                'Accept' => 'application/vnd.github.symmetra-preview+json',
            ],
        ]);

        $content = \GuzzleHttp\json_decode($response->getBody(), true);

        return Label::fromArray($content);
    }
}
