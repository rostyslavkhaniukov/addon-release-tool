<?php

namespace AirSlate\Releaser\Entities;

/**
 * Class Release
 * @package AirSlate\Releaser\Entities
 */
class Release
{
    /** @var mixed */
    public $tagName;

    /**
     * Release constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->tagName = $data['tag_name'];
    }

    /**
     * @param array $data
     * @return Release
     */
    public static function fromArray(array $data): Release
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
