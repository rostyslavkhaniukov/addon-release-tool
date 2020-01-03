<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Services;

use SensioLabs\Consul\ServiceFactory;
use SensioLabs\Consul\Services\KVInterface;

class ConsulKVService
{
    /** @var string */
    private const CONSUL_TOKEN_HEADER = 'X-Consul-Token';

    /** @var KVInterface */
    private $kv;

    public function __construct()
    {
        $consulServiceFactory = new ServiceFactory([
            'base_uri' => getenv('CONSUL_URI'),
            'headers' => [
                self::CONSUL_TOKEN_HEADER => getenv('CONSUL_TOKEN'),
            ],
        ]);
        $this->kv = $consulServiceFactory->get(KVInterface::class);
    }


}
