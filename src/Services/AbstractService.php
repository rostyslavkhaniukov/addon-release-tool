<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Services;

use AirSlate\Releaser\Http\Client as HttpClient;

/**
 * Class AbstractService
 * @package AirSlate\Releaser\Services
 */
class AbstractService
{
    /** @var HttpClient */
    protected $client;

    /** @var string */
    protected $owner;

    /**
     * AbstractService constructor.
     * @param HttpClient $client
     * @param string $owner
     */
    public function __construct(HttpClient $client, string $owner)
    {
        $this->client = $client;
        $this->owner = $owner;
    }
}
