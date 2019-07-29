<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Entities\Git;

/**
 * @package AirSlate\Releaser\Entities\Git
 */
class Commit
{
    /** @var array */
    private $author;

    /** @var array */
    private $commiter;

    /** @var string */
    private $message;

    /** @var array */
    public $tree;

    /** @var string */
    private $url;

    /** @var int */
    private $commentCount;

    /** @var string|null */
    private $sha;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->sha = $data['sha'] ?? null;
        $this->author = $data['author'] ?? [];
        $this->commiter = $data['commiter'] ?? [];
        $this->message = $data['message'] ?? '';
        $this->tree = Tree::fromArray($data['tree'] ?? []);
        $this->url = $data['url'] ?? '';
        $this->commentCount = $data['comment_count'] ?? 0;
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
     * @return string|null
     */
    public function getSha(): ?string
    {
        return $this->sha;
    }

    /**
     * @param string|null $sha
     */
    public function setSha(?string $sha): void
    {
        $this->sha = $sha;
    }
}
