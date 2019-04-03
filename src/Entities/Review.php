<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Entities;

/**
 * Class Review
 * @package AirSlate\Releaser\Entities
 */
class Review
{
    /** @var string */
    public $state;

    /** @var User */
    public $user;

    /**
     * Release constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->state = $data['state'];
        $this->user = User::fromArray($data['user']);
    }

    /**
     * @param array $data
     * @return Review
     */
    public static function fromArray(array $data): Review
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
