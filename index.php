<?php
declare(strict_types=1);

use AirSlate\Releaser\Builder;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

$addons = [
    'notification-addon',
    'prefill-from-source-addons',
    'audit-trail-addon',
    'change-order-addon',
    'document-prefill-addon',
    'dropdown-options-prefill-addon',
    'google-calendar-addon',
    'google-spreadsheets-duplex-addon',
    'google-spreadsheets-postfinish-addon',
    'google-spreadsheets-watcher-addon',
    'jira-addon',
    'lock-slate-bot',
    'export-to-source-addons',
    'packet-delete-addon',
    'recipient-to-role-addon',
    'revoke-access-addon',
    'roles-users-management-addon',
    'send-slate-addon',
    'set-packet-name-addon',
    'slack-notifier-addon',
    'slate-prefill-addon',
    'smartsheet-export-addon',
    'sms-notifier-addon',
    'tags-addon',
    'webhook-addon',
    'weekly-reminder-addon',
    'create-slate-addon',
    'create-slate-another-flow-addon'
];

/*foreach ($addons as $addon) {
    try {
        (new Builder($client, 'airslateinc', $addon))
            ->verify(function (ComposerProcessor $dependencies) {
                return $dependencies
                    ->take('composer.lock')
                    ->checkLocked('pdffiller/mail-api-client');
            });

        echo $addon . "\n";
    } catch (\Throwable $e) {
        var_dump($e->getMessage());
    }
}*/

