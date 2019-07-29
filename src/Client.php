<?php
declare(strict_types=1);

namespace AirSlate\Releaser;

use AirSlate\Releaser\Entities\Commit;
use AirSlate\Releaser\Entities\Diff;
use AirSlate\Releaser\Entities\PullRequest;
use AirSlate\Releaser\Entities\Release;
use AirSlate\Releaser\Http\Client as HttpClient;
use AirSlate\Releaser\Services\BlobsService;
use AirSlate\Releaser\Services\BranchesService;
use AirSlate\Releaser\Services\CheckRunsService;
use AirSlate\Releaser\Services\CommitsService;
use AirSlate\Releaser\Services\ContentsService;
use AirSlate\Releaser\Services\LabelsService;
use AirSlate\Releaser\Services\PullRequestsService;
use AirSlate\Releaser\Services\RefsService;
use AirSlate\Releaser\Services\SearchService;
use AirSlate\Releaser\Services\StatusesService;
use AirSlate\Releaser\Services\TreesService;
use AirSlate\Releaser\Services\WebhookProcessorService;
use AirSlate\Releaser\Services\WebhooksService;
use GuzzleHttp\RequestOptions;

/**
 * Class Client
 *
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

    /** @var PullRequestsService */
    private $pullRequestsService;

    /** @var WebhooksService */
    private $webhooksService;

    /** @var LabelsService */
    private $labelsService;

    /** @var CommitsService */
    private $commitsService;

    /** @var StatusesService */
    private $statusesService;

    /** @var CheckRunsService */
    private $checkRunsService;

    /** @var WebhookProcessorService */
    private $webhooksProcessorService;

    /** @var ContentsService */
    private $contentsService;

    /** @var BranchesService */
    private $branchesService;

    /** @var RefsService */
    private $refsService;

    /** @var BlobsService */
    private $blobsService;

    /** @var TreesService */
    private $treesService;

    /** @var SearchService */
    private $searchService;

    /**
     * Client constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->endpoint = $config['endpoint'] ?? 'https://api.github.com';
        $this->owner = $config['owner'];
        $this->httpClient = $this->configureClient($this->endpoint, $config);
    }

    public function webhookProcessorService()
    {
        if (!$this->webhooksProcessorService) {
            $this->webhooksProcessorService = new WebhookProcessorService($this->httpClient, $this->owner);
        }

        return $this->webhooksProcessorService;
    }

    public function webhooks(): WebhooksService
    {
        if (!$this->webhooksService) {
            $this->webhooksService = new WebhooksService($this->httpClient, $this->owner);
        }

        return $this->webhooksService;
    }

    public function search(): SearchService
    {
        if (!$this->searchService) {
            $this->searchService = new SearchService($this->httpClient, $this->owner);
        }

        return $this->searchService;
    }

    public function labels(): LabelsService
    {
        if (!$this->labelsService) {
            $this->labelsService = new LabelsService($this->httpClient, $this->owner);
        }

        return $this->labelsService;
    }

    public function branches(): BranchesService
    {
        if (!$this->branchesService) {
            $this->branchesService = new BranchesService($this->httpClient);
        }

        return $this->branchesService;
    }

    public function blobs(): BlobsService
    {
        if (!$this->blobsService) {
            $this->blobsService = new BlobsService($this->httpClient);
        }

        return $this->blobsService;
    }

    public function refs(): RefsService
    {
        if (!$this->refsService) {
            $this->refsService = new RefsService($this->httpClient);
        }

        return $this->refsService;
    }

    public function pullRequests(): PullRequestsService
    {
        if (!$this->pullRequestsService) {
            $this->pullRequestsService = new PullRequestsService($this->httpClient, $this->owner);
        }

        return $this->pullRequestsService;
    }

    public function contents(): ContentsService
    {
        if (!$this->contentsService) {
            $this->contentsService = new ContentsService($this->httpClient);
        }

        return $this->contentsService;
    }

    public function commits(): CommitsService
    {
        if (!$this->commitsService) {
            $this->commitsService = new CommitsService($this->httpClient, $this->owner);
        }

        return $this->commitsService;
    }

    public function trees(): TreesService
    {
        if (!$this->treesService) {
            $this->treesService = new TreesService($this->httpClient);
        }

        return $this->treesService;
    }

    public function statuses(): StatusesService
    {
        if (!$this->statusesService) {
            $this->statusesService = new StatusesService($this->httpClient, $this->owner);
        }

        return $this->statusesService;
    }

    public function checkRuns(): CheckRunsService
    {
        if (!$this->checkRunsService) {
            $this->checkRunsService = new CheckRunsService($this->httpClient, $this->owner);
        }

        return $this->checkRunsService;
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

    public function getPRCommits()
    {
        return $this->get(
            $this->pullRequestEndpoint() . '/' . '1' . '/commits'
        );
    }

    public function collectReleasePRs()
    {
        $releases = $this->collectReleases();
        $pullRequests = $this->pullRequests()->closed()->all($this->repo);
        var_dump(count($pullRequests));
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

    public function collectReleasePRs2()
    {
        $releases = Release::fromCollection($this->collectReleases());
        $tag = $releases[1]->tagName;

        $diff = $this->get($this->compareEndpoint($tag, 'master'));
        $diff = Diff::fromArray($diff);
        $shas = array_map(function ($commit) {
            return $commit->sha;
        }, $diff->commits);

        $pullRequests = $this->get($this->pullRequestEndpoint(), [
            'state' => 'closed',
            'base' => 'master',
            'per_page' => 100,
            'sort' => 'updated',
            'direction' => 'desc'
        ]);

        $releasePRs = [];
        $pullRequests = PullRequest::fromCollection($pullRequests);
        foreach ($pullRequests as $pullRequest) {
            /** @var PullRequest $pullRequest */
            if (in_array($pullRequest->mergeCommitSha, $shas, true)
                || in_array($pullRequest->head->sha, $shas, true)) {
                $releasePRs[] = $pullRequest;
            }
        }
        array_walk($releasePRs, function ($item) {
            var_dump($item->title);
        });
        die;
    }

    /**
     * @return array
     */
    public function collectReleases()
    {
        print("Collect releases\n");
        return Release::fromCollection($this->get($this->releasesEndpoint()));
    }

    /**
     * @param Release $release
     * @return Diff
     */
    public function collectDiff(Release $release)
    {
        $diff = $this->get($this->compareEndpoint($release, 'master'));
        return Diff::fromArray($diff);
    }

    /**
     * @param $url
     * @param array $query
     * @return mixed
     */
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

    public function getLastCommit()
    {
        $commits = $this->get($this->commitsEndpoint(), [
            'per_page' => 1,
        ]);
        $commits = Commit::fromCollection($commits);
        return array_shift($commits);
    }

    /**
     * @param $sha
     */
    public function createTag($sha)
    {
        $tag = $this->post($this->tagsEndpoint(), [
            'tag' => 'v0.0.1',
            'message' => 'Test',
            'type' => 'commit',
            'object' => $sha,
        ]);
        var_dump($tag);
    }
}
