<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Services;

use AirSlate\Releaser\EmptyBuilder;
use AirSlate\Releaser\Factories\ProcessorFactory;
use Closure;
use Fluffy\GithubClient\Client as GithubClient;

class TreeBuilder
{

    public function verify(Closure $closure, ?callable $failCallback = null): bool
    {

    }
}
