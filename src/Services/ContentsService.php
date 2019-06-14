<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Services;

use AirSlate\Releaser\Entities\File;
use AirSlate\Releaser\Entities\Label;
use AirSlate\Releaser\Http\Client as HttpClient;

/**
 * @package AirSlate\Releaser\Services
 */
class ContentsService extends AbstractService
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
     * @param string $path
     * @return File
     */
    public function readFile(string $owner, string $repository, string $path): File
    {
        $response = $this->client->get("/repos/{$owner}/{$repository}/contents/{$path}");

        $content = \GuzzleHttp\json_decode($response->getBody(), true);

        return File::fromArray($content);
    }
}
