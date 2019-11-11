<?php

declare(strict_types=1);

namespace AirSlate\Releaser;

class EmptyBuilder
{
    /**
     * @param string $name
     * @param array $arguments
     * @return $this
     */
    public function __call(string $name, array $arguments): self
    {
        return $this;
    }
}
