<?php
declare(strict_types=1);

namespace AirSlate\Releaser;

use AirSlate\Releaser\Entities\PullRequest;

class ReleaseNotesBuilder
{
    /**
     * @param array $pullRequests
     * @return string
     */
    public function build(array $pullRequests): string
    {
        $parsed = array_map(function (PullRequest $pullRequest) {
            return $this->parseTitle($pullRequest);
        }, $pullRequests);

        $parsed = collect($parsed)->groupBy('jira-issue');

        foreach ($parsed as $key => $value) {
            foreach ($value as $bla) {
                var_dump($bla);die;
            }
        }

        return '';
    }

    private function parseTitle(PullRequest $pullRequest)
    {
        preg_match_all('/\[(.*)\](.*)/', $pullRequest->getTitle(), $matches);
        return [
            'all' => $matches[0][0],
            'jira-issue' => $matches[1][0],
            'title' => trim($matches[2][0]),
            'labels' => $pullRequest->getLabels(),
        ];
    }
}
