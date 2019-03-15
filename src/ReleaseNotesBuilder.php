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
        $parsed = array_map(function ($pullRequest) {
            return $this->parseTitle($pullRequest->getTitle());
        }, $pullRequests);

        $parsed = collect($parsed)->groupBy('jira-issue');

        var_dump($parsed);die;

        return '';
    }

    private function parseTitle(string $title)
    {
        preg_match_all('/\[(.*)\](.*)/', $title, $matches);
        return [
            'all' => $matches[0][0],
            'jira-issue' => $matches[1][0],
            'title' => trim($matches[2][0]),
        ];
    }
}
