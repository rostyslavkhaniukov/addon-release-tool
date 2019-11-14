<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Processors;

use AirSlate\Releaser\DTO\WorkingFile;
use AirSlate\Releaser\Exceptions\FileNotFoundInRepositoryException;
use Fluffy\GithubClient\Client;
use Fluffy\GithubClient\Exceptions\DomainException;
use Fluffy\GithubClient\Models\StagedFile;

/**
 * @package AirSlate\Releaser\Processors
 */
class FileProcessor implements ProcessorInterface
{
    /** @var Client */
    private $client;

    /** @var string */
    private $repository;

    /** @var string */
    private $owner;

    /** @var WorkingFile[] */
    protected $workingFiles = [];

    /** @var string */
    private $withFilePath;

    /** @var string */
    protected $withFileBuffer;

    /**
     * @param Client $client
     * @param string $owner
     * @param string $repository
     */
    public function __construct(Client $client, string $owner, string $repository)
    {
        $this->client = $client;
        $this->owner = $owner;
        $this->repository = $repository;
    }

    /**
     * @param string $file
     * @return static
     */
    public function take(string $file)
    {
        try {
            $content = $this->client->contents()->readFile($this->owner, $this->repository, $file);
        } catch (DomainException $e) {
            if ($e->getPrevious()->getCode() === 404) {
                throw new FileNotFoundInRepositoryException($file);
            }
        }

        $this->workingFiles[$file] = new WorkingFile($file, $content->getDecoded());

        return $this;
    }

    /**
     * @param string $file
     * @return $this
     */
    public function with(string $file)
    {
        $content = $this->client->contents()->readFile($this->owner, $this->repository, $file);
        $this->withFilePath = $file;
        $this->withFileBuffer = $content->getDecoded();

        return $this;
    }

    /**
     * @param string $path
     * @param string $content
     * @return static
     */
    public function create(string $path, string $content)
    {
        $this->workingFiles[$path] = new WorkingFile($path, $content);

        return $this;
    }

    public function createFromFile(string $path, string $localPath): self
    {
        $content = file_get_contents($localPath);

        $this->workingFiles[$path] = new WorkingFile($path, $content);

        return $this;
    }

    public function setFromFile(string $localPath): self
    {
        $content = file_get_contents($localPath);
        $keys = array_keys($this->workingFiles);
        $key = reset($keys);
        $this->workingFiles[$key]->setContent($content);

        return $this;
    }

    /**
     * @param string $pattern
     * @param string $replacement
     * @return static
     */
    public function regexReplace(string $pattern, string $replacement)
    {
        array_walk($this->workingFiles, function (WorkingFile $file) use ($pattern, $replacement) {
            $file->setContent(preg_replace($pattern, $replacement, $file->getContent()));
        });

        return $this;
    }

    /**
     * @param string $search
     * @param string $replacement
     * @return static
     */
    public function replace(string $search, string $replacement)
    {
        array_walk($this->workingFiles, function (WorkingFile $file) use ($search, $replacement) {
            $file->setContent(str_replace($search, $replacement, $file->getContent()));
        });

        return $this;
    }

    /**
     * @param string $line
     * @return static
     */
    public function dropLine(string $line)
    {
        return $this->replace($line . "\n", '');
    }

    /**
     * @return StagedFile[]
     */
    public function put(): array
    {
        return array_map(function (WorkingFile $file) {
            $blob = $this->client->blobs()->put(
                $this->owner,
                $this->repository,
                base64_encode($file->getContent()),
                'base64'
            );

            return new StagedFile($file->getPath(), $blob);
        }, array_values($this->workingFiles));
    }
}
