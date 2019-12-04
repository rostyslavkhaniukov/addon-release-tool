<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Services\Dockerfile;

class Dockerfile
{
    /** @var string */
    private $baseImageName;

    /** @var string */
    private $baseImageVersion;

    /**
     * @param string $baseImageName
     * @param string $baseImageVersion
     */
    public function __construct(string $baseImageName, string $baseImageVersion)
    {
        $this->baseImageName = $baseImageName;
        $this->baseImageVersion = $baseImageVersion;
    }

    /**
     * @return string
     */
    public function getBaseImageName(): string
    {
        return $this->baseImageName;
    }

    /**
     * @return string
     */
    public function getBaseImageVersion(): string
    {
        return $this->baseImageVersion;
    }
}
