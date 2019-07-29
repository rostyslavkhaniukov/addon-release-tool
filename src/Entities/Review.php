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

    /** @var string */
    public $submitted_at;

    /**
     * Release constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {

        $this->state = $data['state'];
        $this->user = User::fromArray($data['user']);
        $this->submitted_at = $data['submitted_at'];
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
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
