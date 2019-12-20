<?php

declare(strict_types=1);

namespace AirSlate\Releaser;

use AirSlate\Releaser\Exceptions\NothingToCommitException;
use AirSlate\Releaser\Factories\ProcessorFactory;
use AirSlate\Releaser\Processors\FileProcessor;
use Fluffy\GithubClient\Client;
use Fluffy\GithubClient\Entities\Branch;
use Fluffy\GithubClient\Entities\Git;
use Fluffy\GithubClient\Entities\Ref;
use Fluffy\GithubClient\Enums\FileMode;
use Fluffy\GithubClient\Enums\LeafType;
use Closure;
use Fluffy\GithubClient\Models\StagedFile;
use Symfony\Component\Console\Output\OutputInterface;

class Builder
{
    /** @var Client */
    private $client;

    /** @var string */
    private $repository;

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
    private $stagedFiles = [];

    /** @var string|null */
    private $task = null;

    /** @var OutputInterface|null */
    private $output = null;

    /**
     * @param Closure $closure
     * @return $this|EmptyBuilder
     * @throws \ReflectionException
     */
    public function verify(Closure $closure, ?callable $failCallback = null)
    {
        $factory = new ProcessorFactory();
        $processor = $factory->make($closure, $this->client, $this->client->getOwner(), $this->repository);
        $process = $closure($processor);

        if (!$process) {
            if ($failCallback !== null) {
                $failCallback();
            }
            return new EmptyBuilder();
        }

        return $this;
    }

    public function verifyNext(Closure $closure, ?callable $failCallback = null)
    {
        $factory = new ProcessorFactory();
        $processor = $factory->make($closure, $this->client, $this->client->getOwner(), $this->repository);
        $process = $closure($processor);

        if (!$process) {
            if ($failCallback !== null) {
                $failCallback();
            }
            return new SkipStepBuilder($this);
        }

        return $this;
    }


    public function notify(string $message)
    {
        if ($this->output !== null) {
            $this->output->writeln("<info>{$this->repository}: {$message}.</info>");
        }

        return $this;
    }

    /**
     * @param Closure $closure
     * @return $this
     * @throws \ReflectionException
     */
    public function step(Closure $closure)
    {
        $factory = new ProcessorFactory();
        $processor = $factory->make($closure, $this->client, $this->client->getOwner(), $this->repository);
        $process = $closure($processor);

        $files = array_filter($process->put());
        $this->stagedFiles = array_merge($this->stagedFiles, $files);

        return $this;
    }

    public function collect(Closure $closure)
    {
        $fileProcessor = new FileProcessor($this->client, $this->client->getOwner(), $this->repository);

        $process = $closure($fileProcessor);

        return $process;
    }

    public function __construct(Client $client, string $repository)
    {
        $this->client = $client;
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
        if (empty($this->stagedFiles)) {
            return $this;
        }

        $this->branchName = $branch;
        if ($this->task !== null) {
            $this->branchName = "{$this->task}-{$this->branchName}";
        }

        $this->baseBranchEntity = $this->client->branches()->get(
            $this->client->getOwner(),
            $this->repository,
            $this->baseBranch
        );

        $this->branchRef = $this->client->refs()->createRef(
            $this->client->getOwner(),
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

        if (empty($this->stagedFiles)) {
            throw new NothingToCommitException();
        }

        $commit = $this->client->commits()->get($this->repository, $this->branchRef->objectSha);
        $tree = $this->client->trees()->createTree(
            $this->client->getOwner(),
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
            $this->client->getOwner(),
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
            $this->client->getOwner(),
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
            $this->client->getOwner(),
            $this->repository,
            $name,
            $body,
            $this->branchName,
            $this->baseBranch
        );
    }

    /**
     * @param OutputInterface $output
     * @return self
     */
    public function setOutput(OutputInterface $output): self
    {
        $this->output = $output;

        return $this;
    }
}
