<?php
declare(strict_types=1);

namespace AirSlate\Releaser;

use Fluffy\GithubClient\Client;
use Fluffy\GithubClient\Entities\Branch;
use Fluffy\GithubClient\Entities\Git;
use Fluffy\GithubClient\Entities\Ref;
use Fluffy\GithubClient\Enums\FileMode;
use Fluffy\GithubClient\Enums\LeafType;
use Closure;
use Fluffy\GithubClient\Models\StagedFile;

class Builder
{
    /** @var Client */
    private $client;

    /** @var string */
    private $repository;

    /** @var string */
    private $owner;

    /** @var string */
    private $baseBranch;

    /** @var Ref */
    private $branchRef;

    /** @var Branch */
    private $baseBranchEntity;

    /** @var string */
    private $branchName;

    /** @var Git\Commit */
    private $newCommit;

    /** @var StagedFile[] */
    private $stagedFiles;

    /** @var string|null */
    private $task = null;

    public function step(Closure $closure)
    {
        $fileProcessor = new FileProcessor($this->client, $this->owner, $this->repository);

        $process = $closure($fileProcessor);

        $this->stagedFiles[] = $process->put();

        return $this;
    }

    public function collect(Closure $closure)
    {
        $fileProcessor = new FileProcessor($this->client, $this->owner, $this->repository);

        $process = $closure($fileProcessor);

        return $process;
    }

    public function __construct(Client $client, string $owner, string $repository)
    {
        $this->client = $client;
        $this->owner = $owner;
        $this->repository = $repository;
    }

    public function forTask(string $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function from(string $branch)
    {
        $this->baseBranch = $branch;

        return $this;
    }

    public function branch(string $branch)
    {
        $this->branchName = $branch;
        if ($this->task !== null) {
            $this->branchName = "{$this->task}-{$this->branchName}";
        }

        $this->baseBranchEntity = $this->client->branches()->get($this->owner, $this->repository, $this->baseBranch);

        $this->branchRef = $this->client->refs()->createRef(
            $this->owner,
            $this->repository,
            "refs/heads/{$this->branchName}",
            $this->baseBranchEntity->commit->sha
        );

        return $this;
    }

    public function commit(string $message)
    {
        if ($this->task !== null) {
            $message = "[{$this->task}] {$message}";
        }

        $commit = $this->client->commits()->get($this->repository, $this->branchRef->objectSha);
        $tree = $this->client->trees()->createTree(
            $this->owner,
            $this->repository,
            [
                'base_tree' => $commit->commit->tree->getSha(),
                'tree' => array_map(function (StagedFile $file) {
                    return [
                        'path' => $file->getFilePath(),
                        'mode' => FileMode::BLOB,
                        'type' => LeafType::BLOB,
                        'sha' => $file->getBlob()->getSha(),
                    ];
                }, $this->stagedFiles),
            ]
        );

        $this->newCommit = $this->client->commits()->commit(
            $this->owner,
            $this->repository,
            $tree->getSha(),
            [
                $this->baseBranchEntity->commit->sha,
            ],
            $message
        );

        return $this;
    }

    public function push()
    {
        $this->client->refs()->updateRef(
            $this->owner,
            $this->repository,
            "heads/{$this->branchName}",
            $this->newCommit->getSha()
        );

        return $this;
    }

    public function makePR(string $name, string $body)
    {
        if ($this->task !== null) {
            $name = "[{$this->task}] {$name}";
        }

        $this->client->pullRequests()->create(
            $this->owner,
            $this->repository,
            $name,
            $body,
            $this->branchName,
            $this->baseBranch
        );
    }
}
