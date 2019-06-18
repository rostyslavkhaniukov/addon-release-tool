<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Entities;

/**
 * @package AirSlate\Releaser\Entities
 */
class Diff
{
    /** @var array */
    public $commits;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->commits = Commit::fromCollection($data['commits']);
    }

    /**
     * @param array $data
     * @return Diff
     */
    public static function fromArray(array $data): Diff
    {
        return new static($data);
    }
}
