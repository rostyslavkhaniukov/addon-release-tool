<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Services\VersionUpdater;

class ChangelogVersionUpdater extends AbstractVersionUpdater
{
    /**
     * @param array $fixed
     * @param array $changed
     * @return AbstractVersionUpdater
     */
    public function incrementPatch(array $fixed = [], array $changed = []): AbstractVersionUpdater
    {
        $incrementedVersion = $this->getIncrementedVersion(self::PATCH_INCREMENT_TYPE);
        $this->setVersion($incrementedVersion, $fixed, $changed);
        return $this;
    }

    /**
     * @param array $fixed
     * @param array $changed
     * @return AbstractVersionUpdater
     */
    public function incrementMinor(array $fixed = [], array $changed = []): AbstractVersionUpdater
    {
        $incrementedVersion = $this->getIncrementedVersion(self::MINOR_INCREMENT_TYPE);
        $this->setVersion($incrementedVersion, $fixed, $changed);
        return $this;
    }

    /**
     * @param array $fixed
     * @param array $changed
     * @return AbstractVersionUpdater
     */
    public function incrementMajor(array $fixed = [], array $changed = []): AbstractVersionUpdater
    {
        $incrementedVersion = $this->getIncrementedVersion(self::MINOR_INCREMENT_TYPE);
        $this->setVersion($incrementedVersion, $fixed, $changed);
        return $this;
    }

    /**
     * @return string|null
     */
    protected function getVersion(): ?string
    {
        return array_key_last($this->source);
    }

    /**
     * @param string $version
     * @param array $fixed
     * @param array $changed
     */
    protected function setVersion(string $version, array $fixed = [], array $changed = []): void
    {
        $this->source[$version] = [
            'fixed' => $fixed,
            'changed' => $changed
        ];
    }
}
