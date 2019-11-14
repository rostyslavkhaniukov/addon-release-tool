<?php

declare(strict_types=1);

namespace AirSlate\Releaser;

use SensioLabs\Consul\ServiceFactory;

/**
 * Class CircleCIEnvPatcher
 * @package AirSlate\Releaser
 */
class ConsulAddonFetcher
{
    public function collection()
    {
        $consulServiceFactory = new ServiceFactory([
            'base_uri' => 'http://consul.airslate-stage.xyz',
            'headers' => [
                'X-Consul-Token' => 'aa953920-2a02-8edc-c6b5-a04641f55d73',
            ],
        ]);

        $keyValueStorage = $consulServiceFactory->get('kv');
        $response = $keyValueStorage->get('apps/addons', [
            'raw' => true,
            'recurse' => true
        ])->getBody();
        $keys = json_decode($response, true);

        $addons = [];
        foreach ($keys as $key) {
            preg_match("/apps\/addons\/([a-z-]+-addon)\//m", $key['Key'], $matches);
            if (isset($matches[1])) {
                $addons[] = $matches[1];
            }
        }
        $addons = array_unique($addons);
        $addons = array_values(array_diff($addons, [
            'box-addon',
            'onedrive-addon',
            'gdrive-addon',
            'dropbox-addon',
            'netsuite-addon',
            'roles-addon',
            'weekly-reminder-addon',
            'send-slate-addon',
            'change-order-addon',
        ]));

        $counter = 1;
        foreach ($addons as $addon) {
            echo "{$counter}. {$addon}\n";
            $counter++;
        }
    }
}