<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Exceptions;

use RuntimeException;

class FileNotFoundInRepositoryException extends RuntimeException
{
    /** @var string */
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;

        parent::__construct("File not found: $path");
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}