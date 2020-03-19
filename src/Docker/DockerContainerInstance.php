<?php

namespace AirSlate\Releaser\Docker;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DockerContainerInstance
{
    /** @var DockerContainer */
    private $config;

    /** @var string */
    private $dockerIdentifier;

    /** @var string */
    private $name;

    /**
     * @param DockerContainer $config
     * @param string $dockerIdentifier
     * @param string $name
     */
    public function __construct(
        DockerContainer $config,
        string $dockerIdentifier,
        string $name
    ) {
        $this->config = $config;
        $this->dockerIdentifier = $dockerIdentifier;
        $this->name = $name;
    }

    public function __destruct()
    {
        if ($this->config->stopOnDestruct) {
            $this->stop();
        }
    }

    /**
     * @return Process
     */
    public function stop(): Process
    {
        $fullCommand = "docker stop {$this->getShortDockerIdentifier()}";

        $process = Process::fromShellCommandline($fullCommand);
        $process->run();

        return $process;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return DockerContainer
     */
    public function getConfig(): DockerContainer
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getDockerIdentifier(): string
    {
        return $this->dockerIdentifier;
    }

    /**
     * @return string
     */
    public function getShortDockerIdentifier(): string
    {
        return substr($this->dockerIdentifier, 0, 12);
    }

    /**
     * @param string|array $command
     *
     * @return \Symfony\Component\Process\Process
     */
    public function execute($command): Process
    {
        if (is_array($command)) {
            $command = implode(';', $command);
        }

        $fullCommand = "echo \"{$command}\" | docker exec --interactive {$this->getShortDockerIdentifier()} bash -";

        $process = Process::fromShellCommandline($fullCommand, null, null, null, 10000);

        $process->run();

        return $process;
    }

    /**
     * @param string $pathToPublicKey
     * @param string $pathToAuthorizedKeys
     * @return $this
     */
    public function addPublicKey(
        string $pathToPublicKey,
        string $pathToAuthorizedKeys = '/root/.ssh/authorized_keys'
    ): self {
        $publicKeyContents = trim(file_get_contents($pathToPublicKey));

        $this->execute('echo \''.$publicKeyContents.'\' >> '.$pathToAuthorizedKeys);
        $this->execute("chmod 600 {$pathToAuthorizedKeys}");
        $this->execute("chown root:root {$pathToAuthorizedKeys}");

        return $this;
    }

    /**
     * @param string $content
     * @param string $path
     * @return $this
     */
    public function addFileFromContent(
        string $content,
        string $path
    ): self {
        $a = $this->execute('echo \''.$content.'\' >> '.$path);

        var_dump($a->getErrorOutput());die;

        return $this;
    }

    /**
     * @param string $fileOrDirectoryOnHost
     * @param string $pathInContainer
     * @return $this
     */
    public function addFiles(string $fileOrDirectoryOnHost, string $pathInContainer): self
    {
        $process = Process::fromShellCommandline(
            "docker cp {$fileOrDirectoryOnHost} {$this->getShortDockerIdentifier()}:{$pathInContainer}"
        );
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $this;
    }
}
