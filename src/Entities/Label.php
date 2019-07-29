<?php

namespace AirSlate\Releaser\Entities;

/**
 * Class Label
 * @package AirSlate\Releaser\Entities
 */
class Label
{
    /** @var string */
    public const FEATURE = 'feature';

    /** @var string */
    private $name;

    /** @var string */
    private $color;

    /** @var string */
    private $description;

    /**
     * Label constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->name = (string)$data['name'];
        $this->color = (string)$data['color'];
        $this->description = (string)($data['description'] ?? '');
    }

    /**
     * @param array $data
     * @return Label
     */
    public static function fromArray(array $data): Label
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

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }
}
