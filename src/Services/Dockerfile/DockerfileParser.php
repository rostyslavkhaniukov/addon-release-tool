<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Services\Dockerfile;

class DockerfileParser
{
    private const COMMANDS_MAP = [
        'FROM' => 'parseFrom',
    ];

    /** @var string */
    private $file;

    /** @var string */
    private $baseImageName;

    /** @var string */
    private $baseImageVersion;

    /**
     * @param string $file
     */
    public function __construct(string $file)
    {
        $this->file = $file;
    }

    public function parse()
    {
        $this->file = str_replace("\\\n", '', $this->file);
        $lines = explode("\n", $this->file);

        foreach ($lines as $line) {
            if ($line === '') {
                continue;
            }
            [$command, $parameters] = explode(' ', $line, 2);
            $this->processPair($command, $parameters);
        }
    }

    private function processPair($command, $parameters)
    {
        $handler = self::COMMANDS_MAP[$command] ?? null;
        if ($handler !== null) {
            $this->$handler($parameters);
        }
    }

    private function parseFrom(string $image): void
    {
        [$name, $version] = explode(':', $image);


    }
}
