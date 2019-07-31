<?php
declare(strict_types=1);

namespace AirSlate\Releaser;

use Fluffy\GithubClient\Client;
use Fluffy\GithubClient\Models\StagedFile;

/**
 * Class FileProcessor
 * @package AirSlate\Releaser
 */
class FileProcessor
{
    /** @var Client */
    private $client;

    /** @var string */
    private $repository;

    /** @var string */
    private $owner;

    /** @var string */
    private $fileBuffer;

    /** @var string */
    private $filePath;

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
     * @return FileProcessor
     */
    public function take(string $file): self
    {
        $content = $this->client->contents()->readFile($this->owner, $this->repository, $file);
        $this->filePath = $file;
        $this->fileBuffer = $content->getDecoded();

        return $this;
    }

    /**
     * @param string $path
     * @param string $content
     * @return FileProcessor
     */
    public function create(string $path, string $content): self
    {
        $this->filePath = $path;
        $this->fileBuffer = $content;

        return $this;
    }

    /**
     * @param string $path
     * @return string
     */
    public function findInJson(string $path): string
    {
        $json = json_decode($this->fileBuffer, true);
        $value = $json['packages'];
        $package = array_filter($value, function ($package) use ($path) {
            return $package['name'] === $path;
        });

        return (string)$package[0]['version'];
    }

    /**
     * @param string $pattern
     * @param string $replacement
     * @return $this
     */
    public function regexReplace(string $pattern, string $replacement)
    {
        $this->fileBuffer = preg_replace($pattern, $replacement, $this->fileBuffer);

        return $this;
    }

    /**
     * @param string $search
     * @param string $replacement
     * @return $this
     */
    public function replace(string $search, string $replacement)
    {
        $this->fileBuffer = str_replace($search, $replacement, $this->fileBuffer);

        return $this;
    }

    /**
     * @return StagedFile
     */
    public function put(): StagedFile
    {
        $blob = $this->client->blobs()->put(
            $this->owner,
            $this->repository,
            base64_encode($this->fileBuffer),
            'base64'
        );

        return new StagedFile($this->filePath, $blob);
    }
}
