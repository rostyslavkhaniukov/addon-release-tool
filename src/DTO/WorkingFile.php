<?php

declare(strict_types=1);

namespace AirSlate\Releaser\DTO;

class WorkingFile
{
    /** @var string */
    private $path;

    /** @var string */
    private $content;

    /**
     * @param string $path
     * @param string $content
     */
    public function __construct(string $path, string $content)
    {
        $this->path = $path;
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }
}
