<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Tests;

use AirSlate\Releaser\Services\Dockerfile\DockerfileParser;
use PHPUnit\Framework\TestCase;

class DockerfileParserTest extends TestCase
{
    public function testShouldParseDockerfile()
    {
        $file = file_get_contents(__DIR__ . '/fixtures/Dockerfile');
        $parser = new DockerfileParser($file);
        $parser->parse();
    }
}
