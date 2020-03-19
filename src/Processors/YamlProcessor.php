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

    public function process(string $file, callable $callback)
    {
        $lastFile = $this->workingFiles[$file];
        $encoded = yaml_parse($lastFile->getContent());

        $data = yaml_emit($callback($encoded));

        $this->workingFiles[$file]->setContent(yaml_emit($callback($encoded)));

        return $this;
    }
}
