<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Services;

use AirSlate\Releaser\Entities\PullRequest;
use AirSlate\Releaser\Http\Client as HttpClient;

class PullRequestsService extends AbstractService
{
    public function __construct(HttpClient $client, string $owner)
    {
        parent::__construct($client, $owner);

        $this->client->setQueryParams([
            'state' => 'opened',
            'base' => 'master',
            'per_page' => 20,
            'sort' => 'updated',
            'direction' => 'desc'
        ]);
    }

    public function closed(): PullRequestsService
    {
        $this->client->setQueryParam('state', 'closed');

        return $this;
    }

    /**
     * @param string $repository
     * @return PullRequest[]
     */
    public function all(string $repository): array
    {
        $response = $this->client->get("/repos/{$this->owner}/{$repository}/pulls");

        $content = \GuzzleHttp\json_decode($response->getBody(), true);

        return PullRequest::fromCollection($content);
    }

    public function commits(int $id)
    {
        $response = $this->client->get("/repos/{$this->owner}/{$repository}/pulls/{$id}/commits");
    }
}
