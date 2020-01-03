<?php

declare(strict_types=1);

namespace AirSlate\Releaser;

use SensioLabs\Consul\ServiceFactory;
use SensioLabs\Consul\Services\KVInterface;

/**
 * Class CircleCIEnvPatcher
 * @package AirSlate\Releaser
 */
class ConsulAddonFetcher
{
    public function collection()
    {

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