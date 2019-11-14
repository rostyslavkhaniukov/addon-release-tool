<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Processors;

class YamlProcessor extends FileProcessor
{
    public function withCallback(callable $callback)
    {
        $lastFile = end($this->workingFiles);
        $encoded = yaml_parse($lastFile->getContent());

        return $callback($encoded);
    }
}
