<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Processors;

use Fluffy\GithubClient\Client;
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

    /** @var string */
    protected $fileBuffer;

    /** @var string */
    private $filePath;

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
        $content = $this->client->contents()->readFile($this->owner, $this->repository, $file);
        $this->filePath = $file;
        $this->fileBuffer = $content->getDecoded();

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
     * @return static
     */
    public function regexReplace(string $pattern, string $replacement)
    {
        $this->fileBuffer = preg_replace($pattern, $replacement, $this->fileBuffer);

        return $this;
    }

    /**
     * @param string $search
     * @param string $replacement
     * @return static
     */
    public function replace(string $search, string $replacement)
    {
        $this->fileBuffer = str_replace($search, $replacement, $this->fileBuffer);

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
