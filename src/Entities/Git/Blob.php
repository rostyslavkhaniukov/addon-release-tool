<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Entities\Git;

/**
 * @package AirSlate\Releaser\Entities\Git
 */
class Blob
{
    /** @var string */
    private $sha;

    /** @var string */
    private $url;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->sha = $data['sha'] ?? '';
        $this->url = $data['url'] ?? '';
    }

    /**
     * @param array $data
     * @return Blob
     */
    public static function fromArray(array $data): Blob
    {
        return new static($data);
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getSha(): string
    {
        return $this->sha;
    }

    /**
     * @param string $sha
     */
    public function setSha(string $sha): void
    {
        $this->sha = $sha;
    }
}
