<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Services\VersionUpdater;

use RuntimeException;

abstract class AbstractVersionUpdater
{
    /** @const string  */
    protected const PATCH_INCREMENT_TYPE = 'patch';

    /** @const string  */
    protected const MINOR_INCREMENT_TYPE = 'minor';

    /** @const string  */
    protected const MAJOR_INCREMENT_TYPE = 'major';

    /** @var array */
    protected $source = [];

    /**
     * @param array $source
     * @return $this
     */
    public function setSource(array $source): self
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @return $this
     */
    public function incrementPatch(): self
    {
        $this->setVersion($this->getIncrementedVersion(self::PATCH_INCREMENT_TYPE));
        return $this;
    }

    /**
     * @return $this
     */
    public function incrementMinor(): self
    {
        $this->setVersion($this->getIncrementedVersion(self::MINOR_INCREMENT_TYPE));
        return $this;
    }

    /**
     * @return $this
     */
    public function incrementMajor(): self
    {
        $this->setVersion($this->getIncrementedVersion(self::MAJOR_INCREMENT_TYPE));
        return $this;
    }

    /**
     * @return array
     */
    public function getSource(): array
    {
        return $this->source;
    }

    /**
     * @return string|null
     */
    abstract protected function getVersion(): ?string;

    /**
     * @param string $version
     */
    abstract protected function setVersion(string $version): void;

    /**
     * @param string $incrementType
     * @return int
     */
    protected function parseVersionType(string $incrementType): int
    {
        switch ($incrementType) {
            case self::PATCH_INCREMENT_TYPE:
                return 3;
            case self::MINOR_INCREMENT_TYPE:
                return 2;
            case self::MAJOR_INCREMENT_TYPE:
                return 1;
            default:
                throw new RuntimeException('Incorrect version format');
        }
    }

    /**
     * @param string $incrementType
     * @return string
     */
    protected function getIncrementedVersion(string $incrementType): string
    {
        $oldVersionPieces = null;
        $parsedVersionType = $this->parseVersionType($incrementType);
        $flag = true;
        preg_match('/(\d+)\.(\d+)\.(\d+)/', $this->getVersion(), $oldVersionPieces);
        for ($i = $parsedVersionType; $i <= 3; $i++) {
            if (!isset($oldVersionPieces[$i])) {
                continue;
            }
            if ($flag) {
                $oldVersionPieces[$i] += 1;
            } else {
                $oldVersionPieces[$i] = 0;
            }
        }
        return join('.', array_slice($oldVersionPieces, 1, 3));
    }
}
