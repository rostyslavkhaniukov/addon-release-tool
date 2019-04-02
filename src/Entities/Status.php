<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Entities;

/**
 * Class Status
 * @package AirSlate\Releaser\Entities
 */
class Status
{
    /** @var string */
    public $id;

    /** @var string */
    public $nodeId;

    /** @var string */
    public $state;

    /** @var string */
    public $context;

    /**
     * Release constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->nodeId = $data['node_id'];
        $this->state = $data['state'];
        $this->context = $data['context'];
    }

    /**
     * @param array $data
     * @return Status
     */
    public static function fromArray(array $data): Status
    {
        return new static($data);
    }

    /**
     * @param array $data
     * @return array
     */
    public static function fromCollection(array $data): array
    {
        return array_map(function (array $item) {
            return static::fromArray($item);
        }, $data);
    }
}
