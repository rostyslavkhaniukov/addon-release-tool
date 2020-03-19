<?php

declare(strict_types=1);

namespace Spatie\Docker\Exceptions;

use AirSlate\Releaser\Docker\DockerContainer;
use Exception;
use Symfony\Component\Process\Process;

class CouldNotStartDockerContainer extends Exception
{
    /**
     * @param DockerContainer $container
     * @param Process $process
     * @return static
     */
    public static function processFailed(DockerContainer $container, Process $process)
    {
        return new static(
            "Could not start docker container for image {$container->image}`. 
            Process output: `{$process->getErrorOutput()}`"
        );
    }
}
