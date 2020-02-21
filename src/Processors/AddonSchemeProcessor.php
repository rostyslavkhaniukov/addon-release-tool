<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Processors;

use AirSlate\Releaser\DTO\WorkingFile;
use AirSlate\Releaser\Services\SchemesPathsFetcher;
use Closure;
use Fluffy\GithubClient\Client;

class AddonSchemeProcessor extends JsonProcessor
{
    /** @var SchemesPathsFetcher */
    private $schemesFetcher;

    /**
     * @param Client $client
     * @param string $owner
     * @param string $repository
     */
    public function __construct(Client $client, string $owner, string $repository, string $sha)
    {
        parent::__construct($client, $owner, $repository, $sha);
        
        $this->schemesFetcher = new SchemesPathsFetcher();
    }

    public function takeAll()
    {
        $schemes = $this->schemesFetcher->fetch($this->repository);
        foreach ($schemes as $scheme) {
            $this->take($scheme);
        }

        return $this;
    }

    public function map(Closure $callback)
    {
        array_walk($this->workingFiles, function (WorkingFile $file) use ($callback) {
            $content = json_decode($file->getContent(), true);

            $result = json_encode($callback($content), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $result = preg_replace('/^(  +?)\\1(?=[^ ])/m', '$1', $result);
            $result .= "\n";

            $file->setContent($result);
        });

        return $this;
    }
}
