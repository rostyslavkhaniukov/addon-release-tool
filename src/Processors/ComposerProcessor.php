<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Processors;

use AirSlate\Releaser\Docker\DockerContainerInstance;
use AirSlate\Releaser\Services\DependenciesUpdater;
use Fluffy\GithubClient\Client;

class ComposerProcessor extends JsonProcessor
{
    private $composerJsonContent;
    private $composerLockContent;

    /** @var DockerContainerInstance */
    private $containerInstance;

    public function __construct(
        Client $client,
        string $owner,
        string $repository
    ) {
        parent::__construct($client, $owner, $repository, '');

        $this->take('composer.json')->take('composer.lock');

        $this->composerJsonContent = json_decode($this->workingFiles['composer.json']->getContent(), true);
        $this->composerLockContent = json_decode($this->workingFiles['composer.lock']->getContent(), true);
    }

    public function setContainerInstance(DockerContainerInstance $containerInstance): void
    {
        $this->containerInstance = $containerInstance;
    }

    public function ensure(string $dependency, string $version, string $githubToken, bool $isDev = false): self
    {
        $packagesKey = $isDev ? 'require-dev' : 'require';
        $this->composerJsonContent[$packagesKey][$dependency] = $version;

        $newContent = json_encode($this->composerJsonContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
        $this->workingFiles['composer.json']->setContent($newContent);

        $composer = new DependenciesUpdater();
        $result = $composer->update(
            $this->workingFiles['composer.json']->getContent(),
            $this->workingFiles['composer.lock']->getContent(),
            $dependency,
            $githubToken,
            $this->containerInstance
        );

        $this->workingFiles['composer.lock']->setContent($result);

        return $this;
    }

    public function checkLocked(string $checkedPackage): bool
    {
        $packages = $this->composerJsonContent['packages'] ?? [];
        foreach ($packages as $package) {
            if ($package['name'] === $checkedPackage) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $checkedPackage
     * @return string|null
     */
    public function getLockedVersion(string $checkedPackage): ?string
    {
        $packages = $this->composerLockContent['packages'] ?? [];

        foreach ($packages as $package) {
            if ($package['name'] === $checkedPackage) {
                return $package['version'];
            }
        }

        return null;
    }
}
