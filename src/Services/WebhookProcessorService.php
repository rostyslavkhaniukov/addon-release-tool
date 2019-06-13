<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Services;

use AirSlate\Releaser\Entities\WebhookEvent;
use AirSlate\Releaser\Http\Client as HttpClient;

/**
 * Class WebhookProcessorService
 * @package AirSlate\Releaser\Services
 */
class WebhookProcessorService extends AbstractService
{
    /**
     * @param HttpClient $client
     * @param string $owner
     */
    public function __construct(HttpClient $client, string $owner)
    {
        parent::__construct($client, $owner);
    }

    public function bla()
    {
        $bla = file_get_contents(__DIR__ . '/../../fixtures/rejected.json');
        $bla = json_decode($bla, true);
        $bla = WebhookEvent::fromArray($bla);
        var_dump($bla);
    }
}
