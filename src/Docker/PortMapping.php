<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Docker;

class PortMapping
{
    /** @var int */
    private $portOnHost;

    /** @var int */
    private $portOnDocker;

    /**
     * @param int $portOnHost
     * @param int $portOnDocker
     */
    public function __construct(int $portOnHost, int $portOnDocker)
    {
        $this->portOnHost = $portOnHost;
        $this->portOnDocker = $portOnDocker;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "-p {$this->portOnHost}:{$this->portOnDocker}";
    }
}
