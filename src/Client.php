<?php
declare(strict_types=1);

namespace AirSlate\Releaser;

use AirSlate\Releaser\Entities\Diff;
use AirSlate\Releaser\Entities\PullRequest;
use AirSlate\Releaser\Entities\Release;
use AirSlate\Releaser\Http\Client as HttpClient;
use GuzzleHttp\RequestOptions;

/**
 * Class Client
 * @package AirSlate\Releaser
 */
class Client
{
    /** @var string */
    private $endpoint;

    /** @var string */
    private $owner;

    /** @var string */
    private $repo;

    /** @var HttpClient */
    private $httpClient;

    /**
     * Client constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->endpoint = $config['endpoint'] ?? 'https://api.github.com';
        $this->owner = $config['owner'];
        $this->repo = $config['repo'];

        $this->httpClient = $this->configureClient($this->endpoint, $config);

        $prs = $this->collectReleasePRs();
        $builder = new ReleaseNotesBuilder();
        var_dump($builder->build($prs));
    }

    /**
     * @param $baseUri
     * @param array $config
     * @return HttpClient
     */
    public function configureClient($baseUri, array $config = []): HttpClient
    {
        $httpClient = new HttpClient([
            'base_uri' => $this->prepareBaserUri($baseUri),
            'headers' => $this->prepareHeaders($config),
            'connect_timeout' => $config['connectTimeout'] ?? 30,
            'request_timeout' => $config['requestTimeout'] ?? 30,
        ]);

        return $httpClient;
    }

    /**
     * @param array $config
     * @return array
     */
    private function getDefaultHeaders(array $config): array
    {
        return [
            'Authorization' => 'token ' . ($config['token'] ?? ''),
            'User-Agent' => 'airslateinc/addon-releaser',
        ];
    }
    /**
     * @param array $config
     * @return array
     */
    private function prepareHeaders(array $config): array
    {
        return array_merge(
            $this->getDefaultHeaders($config),
            $config['headers'] ?? []
        );
    }

    /**
     * @param $baseUri
     * @return string
     */
    private function prepareBaserUri(string $baseUri): string
    {
        return rtrim($baseUri, '/') . '/';
    }

    public function pullRequestEndpoint()
    {
          return '/repos/' . $this->owner . '/' . $this->repo . '/pulls';
    }

    public function releasesEndpoint()
    {
        return '/repos/' . $this->owner . '/' . $this->repo . '/releases';
    }

    public function compareEndpoint($branch1, $branch2)
    {
        if ($branch1 instanceof Release) {
            $branch1 = $branch1->tagName;
        }

        return '/repos/' . $this->owner . '/' . $this->repo . '/compare/' . $branch1 . '...' . $branch2;
    }

    public function getPRCommits()
    {
        return $this->get(
            $this->pullRequestEndpoint() . '/' . '1' . '/commits'
        );
    }

    public function collectReleasePRs()
    {
        $releases = $this->collectReleases();
        $pullRequests = $this->collectPRs();
        $diff = $this->collectDiff($releases[2]);


        $shas = array_map(function ($commit) {
            return $commit->sha;
        }, $diff->commits);

        $releasePR = [];
        foreach ($pullRequests as $pullRequest) {
            /** @var PullRequest $pullRequest */
            if (in_array($pullRequest->head->sha, $shas, true)
                || in_array($pullRequest->mergeCommitSha, $shas, true)
            ) {
                $releasePR[] = $pullRequest;
            }
        }

        return $releasePR;
    }

    /**
     * @return PullRequest[]
     */
    public function collectPRs(): array
    {
        $pullRequests = $this->get($this->pullRequestEndpoint(), [
            'state' => 'closed',
            'base' => 'master',
            'per_page' => 20,
            'sort' => 'updated',
            'direction' => 'desc'
        ]);
        return PullRequest::fromCollection($pullRequests);
    }

    public function collectReleases()
    {
        return Release::fromCollection($this->get($this->releasesEndpoint()));
    }

    public function collectDiff(Release $release)
    {
        $diff = $this->get($this->compareEndpoint($release, 'master'));
        return Diff::fromArray($diff);
    }

    public function get($url, $query = [])
    {
        $response = $this->httpClient->get($url, [
            RequestOptions::QUERY => $query,
        ]);
        $content = \GuzzleHttp\json_decode($response->getBody(), true);
        return $content;
    }

    public function post($url)
    {
        $response = $this->httpClient->post($url, [
            RequestOptions::JSON => [
                'title' => 'Preparing release pull request...',
                'head' => 'develop',
                'base' => 'master',
            ],
        ]);
        $content = \GuzzleHttp\json_decode($response->getBody(), true);
        return $content;
    }
}
