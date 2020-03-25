<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Services;

use AirSlate\Releaser\Docker\DockerContainerInstance;

class DependenciesUpdater
{
    /**
     * @param string $jsonContent
     * @param string|null $lockContent
     * @param string|null $dependency
     * @param string $token
     * @param DockerContainerInstance $containerInstance
     * @return string
     */
    public function update(
        string $jsonContent,
        ?string $lockContent,
        ?string $dependency,
        string $token,
        DockerContainerInstance $containerInstance
    ): string {
        $composerPath = tempnam(sys_get_temp_dir(), 'composer');
        file_put_contents($composerPath . '.json', $jsonContent);
        $containerInstance->addFiles($composerPath . '.json', 'composer.json');

        if ($lockContent !== null) {
            file_put_contents($composerPath . '.lock', $lockContent);
            $containerInstance->addFiles($composerPath . '.lock', 'composer.lock');
        }

        $containerInstance->execute("composer config --global github-oauth.github.com {$token}");
        $command = ($dependency === null) ? 'composer update' : "composer update {$dependency}";
        $containerInstance->execute($command);
        $composerLockContent = $containerInstance->execute('cat composer.lock');
        $json = $composerLockContent->getOutput();
        return $json;
    }
}
