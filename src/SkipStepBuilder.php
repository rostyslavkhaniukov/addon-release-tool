<?php

declare(strict_types=1);

namespace AirSlate\Releaser;

class SkipStepBuilder
{
    /** @var Builder */
    private $builder;

    /**
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return Builder
     */
    public function __call(string $name, array $arguments): Builder
    {
        return $this->builder;
    }
}
