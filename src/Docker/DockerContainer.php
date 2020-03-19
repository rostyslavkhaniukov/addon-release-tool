<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Docker;

use Spatie\Docker\Exceptions\CouldNotStartDockerContainer;
use Symfony\Component\Process\Process;

class DockerContainer
{
    /** @var string */
    public $image = '';

    /** @var string */
    public $name = '';

    /** @var bool */
    public $daemonize = true;

    /** @var PortMapping[] */
    public $portMappings = [];

    /** @var bool */
    public $cleanUpAfterExit = true;

    /** @var bool */
    public $stopOnDestruct = false;

    /**
     * @param string $image
     * @param string $name
     */
    public function __construct(string $image, string $name = '')
    {
        $this->image = $image;
        $this->name = $name;
    }

    /**
     * @param mixed ...$args
     * @return static
     */
    public static function create(...$args): self
    {
        return new static(...$args);
    }

    /**
     * @param string $image
     * @return $this
     */
    public function image(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param bool $daemonize
     * @return $this
     */
    public function daemonize(bool $daemonize = true): self
    {
        $this->daemonize = $daemonize;

        return $this;
    }

    /**
     * @return $this
     */
    public function doNotDaemonize(): self
    {
        $this->daemonize = false;

        return $this;
    }

    /**
     * @param bool $cleanUpAfterExit
     * @return $this
     */
    public function cleanUpAfterExit(bool $cleanUpAfterExit): self
    {
        $this->cleanUpAfterExit = $cleanUpAfterExit;

        return $this;
    }

    /**
     * @return $this
     */
    public function doNotCleanUpAfterExit(): self
    {
        $this->cleanUpAfterExit = false;

        return $this;
    }

    /**
     * @param int $portOnHost
     * @param $portOnDocker
     * @return $this
     */
    public function mapPort(int $portOnHost, $portOnDocker): self
    {
        $this->portMappings[] = new PortMapping($portOnHost, $portOnDocker);

        return $this;
    }

    /**
     * @param bool $stopOnDestruct
     * @return $this
     */
    public function stopOnDestruct(bool $stopOnDestruct = true): self
    {
        $this->stopOnDestruct = $stopOnDestruct;

        return $this;
    }

    /**
     * @return string
     */
    public function getStartCommand(): string
    {
        return "docker run {$this->getExtraOptions()} {$this->image}";
    }

    /**
     * @return DockerContainerInstance
     * @throws CouldNotStartDockerContainer
     */
    public function start(): DockerContainerInstance
    {
        $command = $this->getStartCommand();

        $process = Process::fromShellCommandline($command);

        $process->run();

        if (! $process->isSuccessful()) {
            throw CouldNotStartDockerContainer::processFailed($this, $process);
        }

        $dockerIdentifier = $process->getOutput();

        return new DockerContainerInstance(
            $this,
            $dockerIdentifier,
            $this->name
        );
    }

    /**
     * @return string
     */
    protected function getExtraOptions(): string
    {
        $extraOptions = [];

        if (count($this->portMappings)) {
            $extraOptions[] = implode(' ', $this->portMappings);
        }

        if ($this->name !== '') {
            $extraOptions[] = "--name {$this->name}";
        }

        if ($this->daemonize) {
            $extraOptions[] = '-d';
        }

        if ($this->cleanUpAfterExit) {
            $extraOptions[] = '--rm';
        }

        return implode(' ', $extraOptions);
    }
}
