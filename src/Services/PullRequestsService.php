<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Services;

use AirSlate\Releaser\Entities\Commit;
use AirSlate\Releaser\Entities\PullRequest;
use AirSlate\Releaser\Entities\Review;
use AirSlate\Releaser\Http\Client as HttpClient;

class PullRequestsService extends AbstractService
{
    private $repository;

    public function __construct(HttpClient $client, string $owner, string $repository)
    {
        parent::__construct($client, $owner);
        $this->repository = $repository;

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
     * @return PullRequest[]
     */
    public function all(): array
    {
        $response = $this->client->get("/repos/{$this->owner}/{$this->repository}/pulls");

        $content = \GuzzleHttp\json_decode($response->getBody(), true);

        return PullRequest::fromCollection($content);
    }

    public function commits(int $id)
    {
        $response = $this->client->get("/repos/{$this->owner}/{$this->repository}/pulls/{$id}/commits");

        $content = \GuzzleHttp\json_decode($response->getBody(), true);

        return Commit::fromArray($content);
    }

    /**
     * @param int $id
     * @return Review[]
     */
    public function reviews(int $id)
    {
        $response = $this->client->get("/repos/{$this->owner}/{$this->repository}/pulls/{$id}/reviews");

        $content = \GuzzleHttp\json_decode($response->getBody(), true);

        return Review::fromCollection($content);
    }
}
