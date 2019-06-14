<?php
declare(strict_types=1);

namespace AirSlate\Releaser;

use AirSlate\Releaser\Enums\FileMode;
use AirSlate\Releaser\Enums\LeafType;

class Builder
{
    /** @var Client */
    private $client;

    /** @var string */
    private $fileBuffer;

    /** @var string */
    private $repository;

    /** @var string */
    private $owner;

    /** @var string */
    private $baseBranch;

    /** @var Entities\Ref */
    private $branchRef;

    /** @var string */
    private $filePath;

    /** @var Entities\Git\Blob */
    private $blob;

    public function __construct(Client $client, string $owner, string $repository)
    {
        $this->client = $client;
        $this->owner = $owner;
        $this->repository = $repository;
    }

    public function from(string $branch)
    {
        $this->baseBranch = $branch;

        return $this;
    }

    public function branch(string $branch)
    {
        $baseBranchEntity = $this->client->branches()->get($this->owner, $this->repository, $this->baseBranch);

        $this->branchRef = $this->client->refs()->createRef(
            $this->owner,
            $this->repository,
            "refs/heads/{$branch}",
            $baseBranchEntity->commit->sha
        );

        return $this;
    }

    public function commit()
    {
        $commit = $this->client->commits()->get($this->repository, $this->branchRef->objectSha);

        $this->client->trees()->createTree(
            $this->owner,
            $this->repository,
            [
                'base_tree' => $commit->commit->tree->getSha(),
                'tree' => [
                    [
                        'path' => $this->filePath,
                        'mode' => FileMode::BLOB,
                        'type' => LeafType::BLOB,
                        'sha' => $this->blob->getSha(),
                    ]
                ],
            ]);

        $newCommit = $this->client->commits()->commit(
            $this->owner,
            $this->repository,
            'f15743fbdbb20659092d10f8c53b8928136940cd',
            [
                $s->commit->sha,
            ]
        );
    }

    public function take(string $file): self
    {
        $content = $this->client->contents()->readFile($this->owner, $this->repository, $file);
        $this->filePath = $file;
        $this->fileBuffer = $content->getDecoded();

        return $this;
    }

    public function replace(string $pattern, string $replacement)
    {
        $this->fileBuffer = preg_replace($pattern, $replacement, $this->fileBuffer);

        return $this;
    }

    public function put()
    {
        $this->blob = $this->client->blobs()->put(
            $this->owner,
            $this->repository,
            base64_encode($this->fileBuffer),
            'base64'
        );
    }
}
