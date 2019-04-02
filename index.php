<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use AirSlate\Releaser\Client;

$client = new Client([
    'owner' => 'airslateinc',
    'token' => '',
]);

function addLabel(Client $client, $newLabel)
{
    $addons = [
        'google-spreadsheets-postfinish-addon',
        'google-spreadsheets-watcher-addon',
        'webhook-addon',
        'notification-addon',
        'prefill-from-source-addons',
        'google-spreadsheets-duplex-addon',
        'jira-addon',
        'revoke-access-addon',
        'dropdown-options-prefill-addon',
        'slate-prefill-addon',
        'document-prefill-addon',
        'slack-notifier-addon',
        'send-slate-addon',
        'change-order-addon',
        'roles-management-addon',
        'roles-users-management-addon',
        'packet-delete-addon',
        'set-packet-name-addon',
        'weekly-reminder-addon',
        'google-calendar-addon',
        'lock-fields-addon',
        'sms-notifier-addon',
    ];

    foreach ($addons as $addon) {
        $labels = $client->labels()->all($addon);
        $found = false;
        foreach ($labels as $label) {
            if ($label->getName() === $newLabel['name']) {
                $found = true;
                if ($label->getColor() !== $newLabel['color']
                    || $label->getDescription() !== $newLabel['description']) {
                    $client->labels()->update($addon, $label->getName(), $newLabel['color'], $newLabel['description']);
                }
            }
        }
        if (!$found) {
            $client->labels()->create($addon, $newLabel['name'], $newLabel['color'], $newLabel['description']);
        }
    }
}

addLabel($client, [
    'name' => 'approved',
    'color' => '008672',
    'description' => 'Approved by applications and platform teams',
]);

addLabel($client, [
    'name' => 'waiting for platform review',
    'color' => '1d76db',
    'description' => 'Approved by applications team and waiting for platform team',
]);
