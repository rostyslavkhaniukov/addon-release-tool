<?php
declare(strict_types=1);

namespace AirSlate\Releaser;

use AirSlate\Releaser\Models\StagedFile;

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
