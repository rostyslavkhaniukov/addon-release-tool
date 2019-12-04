<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Exceptions;

use RuntimeException;

class NothingToCommitException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct("Nothing to commit");
    }
}