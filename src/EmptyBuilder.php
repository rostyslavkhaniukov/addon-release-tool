<?php

declare(strict_types=1);

namespace AirSlate\Releaser;

class EmptyBuilder
{
    public function __call(string $name, array $arguments): self
    {
        return $this;
    }
}
