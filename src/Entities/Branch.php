<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Entities;

/**
 * @package AirSlate\Releaser\Entities
 */
class Branch
{
    /** @var Commit */
    public $commit;

    /** @var string */
    private $name;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->name = $data['name'] ?? '';
        $this->commit = Commit::fromArray($data['commit']);
    }

    /**
     * @param array $data
     * @return Branch
     */
    public static function fromArray(array $data): Branch
    {
        return new static($data);
    }
}
