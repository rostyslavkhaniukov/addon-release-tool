<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Services;

use AirSlate\Releaser\Entities\Branch;
use AirSlate\Releaser\Entities\File;
use AirSlate\Releaser\Entities\Label;
use AirSlate\Releaser\Http\Client as HttpClient;
use GuzzleHttp\RequestOptions;

/**
 * @package AirSlate\Releaser\Services
 */
class BlobsService extends AbstractService
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
     * @param string $content
     * @param string $encoding
     * @return Branch
     */
    public function put(string $owner, string $repository, string $content, string $encoding): Branch
    {
        $response = $this->client->post("/repos/{$owner}/{$repository}/git/blobs", [
            RequestOptions::JSON => [
                'content' => $content,
                'encoding' => $encoding,
            ]
        ]);

        $content = \GuzzleHttp\json_decode($response->getBody(), true);

        var_dump($content);die;

        return Branch::fromArray($content);
    }
}
