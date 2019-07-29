<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Entities;

/**
 * @package AirSlate\Releaser\Entities
 */
class File
{
    /** @var string */
    private $name;

    /** @var string */
    private $sha;

    /** @var string */
    private $content;

    /** @var string */
    private $encoding;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->sha = $data['sha'];
        $this->content = $data['content'];
        $this->encoding = $data['encoding'];
    }

    /**
     * @param array $data
     * @return File
     */
    public static function fromArray(array $data): File
    {
        return new static($data);
    }

    /**
     * @return string|null
     */
    public function getDecoded(): ?string
    {
        if ($this->encoding === 'base64') {
            return base64_decode($this->content, true);
        }

        return null;
    }
}
