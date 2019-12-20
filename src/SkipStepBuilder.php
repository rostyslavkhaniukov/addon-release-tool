<?php

declare(strict_types=1);

namespace AirSlate\Releaser;

class SkipStepBuilder
{
    /** @var Builder */
    private $builder;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return $this
     */
    public function __call(string $name, array $arguments): self
    {
        return $this->builder;
    }
}
