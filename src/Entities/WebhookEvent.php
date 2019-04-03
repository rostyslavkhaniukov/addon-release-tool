<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Entities;

/**
 * Class WebhookEvent
 * @package AirSlate\Releaser\Entities
 */
class WebhookEvent
{
    /** @var string */
    public $action;

    /** @var Review */
    public $review;

    /**
     * Release constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->action = $data['action'];
        $this->review = Review::fromArray($data['review']);
    }

    /**
     * @param array $data
     * @return WebhookEvent
     */
    public static function fromArray(array $data): WebhookEvent
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
