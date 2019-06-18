<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Services;

use AirSlate\Releaser\Entities\Commit;
use AirSlate\Releaser\Entities\Label;
use AirSlate\Releaser\Entities\Ref;
use AirSlate\Releaser\Entities\Git;
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

    /**
     * @param string $repository
     * @param string $sha
     * @return Commit
     */
    public function get(string $repository, string $sha): Commit
    {
        $response = $this->client->get("/repos/{$this->owner}/{$repository}/commits/{$sha}");

        $content = \GuzzleHttp\json_decode($response->getBody(), true);

        return Commit::fromArray($content);
    }

    /**
     * @param string $owner
     * @param string $repository
     * @param string $treeSha
     * @param array $parents
     * @param string $message
     * @return Git\Commit
     */
    public function commit(string $owner, string $repository, string $treeSha, array $parents, string $message): Git\Commit
    {
        $response = $this->client->post("/repos/{$owner}/{$repository}/git/commits", [
            RequestOptions::JSON => [
                'message' => $message,
                'tree' => $treeSha,
                'parents' => $parents
            ]
        ]);

        $content = \GuzzleHttp\json_decode($response->getBody(), true);

        return Git\Commit::fromArray($content);
    }
}
