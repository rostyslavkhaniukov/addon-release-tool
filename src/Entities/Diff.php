<?php

namespace AirSlate\Releaser\Entities;

/**
 * Class Diff
 * @package AirSlate\Releaser\Entities
 */
class Diff
{
    public $commits;

    public function __construct(array $data)
    {
        $this->commits = Commit::fromCollection($data['commits']);
    }

    public static function fromArray(array $data): Diff
    {
        return new static($data);
    }
}
