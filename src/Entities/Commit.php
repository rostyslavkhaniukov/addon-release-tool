<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Entities;

/**
 * @package AirSlate\Releaser\Entities
 */
class Commit
{
    public $sha;
    public $message;
    public $title;

    /** @var Git\Commit */
    public $commit;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->sha = $data['sha'];
        $this->title = $data['title'] ?? '';
        $this->message = $data['commit']['message'] ?? '';
        $this->commit = Git\Commit::fromArray($data['commit']);
    }

    /**
     * @param array $data
     * @return Commit
     */
    public static function fromArray(array $data): Commit
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
