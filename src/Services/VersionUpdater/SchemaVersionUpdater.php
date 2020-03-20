<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Services\VersionUpdater;

class SchemaVersionUpdater extends AbstractVersionUpdater
{
    /**
     * @return string|null
     */
    protected function getVersion(): ?string
    {
        return $this->source['data']['attributes']['version'] ?? null;
    }

    /**
     * @param string $version
     */
    protected function setVersion(string $version): void
    {
        $this->source['data']['attributes']['version'] = $version;
    }
}
