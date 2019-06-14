<?php

namespace AirSlate\Releaser\Entities;

use AirSlate\Releaser\Services\ContentsService;

/**
 * Class Branch
 * @package AirSlate\Releaser\Entities
 */
class Branch
{
    /** @var Commit */
    public $commit;

    /** @var string */
    private $name;

    public function __construct(array $data)
    {
        $this->name = $data['name'] ?? '';
        $this->commit = Commit::fromArray($data['commit']);
    }

    public static function fromArray(array $data): Branch
    {
        return new static($data);
    }
}
