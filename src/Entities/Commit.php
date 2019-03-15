<?php

namespace AirSlate\Releaser\Entities;

/**
 * Class Commit
 * @package AirSlate\Releaser\Entities
 */
class Commit
{
    public $sha;
    public $message;
    public $title;

    public function __construct(array $data)
    {
        $this->sha = $data['sha'];
        $this->title = $data['title'] ?? '';
        $this->message = $data['commit']['message'] ?? '';
    }

    public static function fromArray(array $data): Commit
    {
        return new static($data);
    }

    public static function fromCollection(array $data): array
    {
        return array_map(function (array $item) {
            return static::fromArray($item);
        }, $data);
    }
}
