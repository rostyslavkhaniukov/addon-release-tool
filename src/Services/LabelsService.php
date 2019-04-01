<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Services;

use AirSlate\Releaser\Entities\Label;
use AirSlate\Releaser\Entities\Webhook;
use AirSlate\Releaser\Http\Client as HttpClient;

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
        $response = $this->client->get("/repos/{$this->owner}/{$repository}/labels");

        $content = \GuzzleHttp\json_decode($response->getBody(), true);

        return Label::fromCollection($content);
    }
}
