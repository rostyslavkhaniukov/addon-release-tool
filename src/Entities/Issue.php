<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Entities;

/**
 * Class Issue
 * @package AirSlate\Releaser\Entities
 */
class Issue
{
    private $number;

    private $title;

    private $htmlUrl;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->number = $data['number'];
        $this->title = $data['title'];
        $this->htmlUrl = $data['html_url'];
    }

    /**
     * @param array $data
     * @return Issue
     */
    public static function fromArray(array $data): Issue
    {
        return new static($data);
    }

    /**
     * @param array $data
     * @return Issue[]
     */
    public static function fromCollection(array $data): array
    {
        return array_map(function (array $item) {
            return static::fromArray($item);
        }, $data);
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getHtmlUrl()
    {
        return $this->htmlUrl;
    }

    /**
     * @param mixed $htmlUrl
     */
    public function setHtmlUrl($htmlUrl): void
    {
        $this->htmlUrl = $htmlUrl;
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param mixed $number
     */
    public function setNumber($number): void
    {
        $this->number = $number;
    }
}
