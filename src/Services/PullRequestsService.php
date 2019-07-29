<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Services;

use AirSlate\Releaser\Entities\Commit;
use AirSlate\Releaser\Entities\PullRequest;
use AirSlate\Releaser\Entities\Review;
use AirSlate\Releaser\Http\Client as HttpClient;
use GuzzleHttp\RequestOptions;

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

    /**
     * @param string $repository
     * @param int $pullId
     * @return Commit
     */
    public function commits(string $repository, int $pullId): Commit
    {
        $response = $this->client->get("/repos/{$this->owner}/{$repository}/pulls/{$pullId}/commits");

        $content = \GuzzleHttp\json_decode($response->getBody(), true);

        return Commit::fromArray($content);
    }

    public function create(string $owner, string $repository, string $title, string $body, string $head, string $base)
    {
        $response = $this->client->post("/repos/{$owner}/{$repository}/pulls", [
            RequestOptions::JSON => [
                'title' => $title,
                'body' => $body,
                'head' => $head,
                'base' => $base,
            ]
        ]);

        $content = \GuzzleHttp\json_decode($response->getBody(), true);

        //return Commit::fromArray($content);
    }

    /**
     * @param string $repository
     * @param int $id
     * @return Review[]
     */
    public function reviews(string $repository, int $id)
    {
        $response = $this->client->get("/repos/{$this->owner}/{$repository}/pulls/{$id}/reviews");

        $content = \GuzzleHttp\json_decode($response->getBody(), true);

        return Review::fromCollection($content);
    }
}
