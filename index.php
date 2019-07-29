<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use AirSlate\Releaser\Builder;
use AirSlate\Releaser\Client;
use AirSlate\Releaser\FileProcessor;

$client = new Client([
    'owner' => 'airslateinc',
    'token' => '',
]);

/*$addons = [
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
    'notification-addon',
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
];*/

$addons = [
    'prefill-from-source-addons',
];

/*$branch = $client->branches()->get('airslateinc', $addons[0], 'master');
$commit = $client->commits()->get($addons[0], $branch->commit->sha);
$tree = $client->trees()->get('airslateinc', $addons[0], $commit->commit->tree->getSha());

foreach ($tree->getTree() as $item) {
    if (pathinfo($item['path'])['extension'] === 'php') {
        echo $item['path'] . "\n";
    }
}

$client->contents()->getArchiveLink('airslateinc', $addons[0]);*/

function getDirContents($dir, &$results = array()){
    $files = scandir($dir);

    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        if(!is_dir($path)) {
            $results[] = $path;
        } else if($value != "." && $value != "..") {
            getDirContents($path, $results);
        }
    }

    return $results;
}

$a = getDirContents('airslateinc-prefill-from-source-addons-44e0727c74581f57a8f1fdc0f015f6a92c512a15');
$a = array_filter($a, function ($b) {
    $c = pathinfo($b);

    $ll = 'airslateinc-prefill-from-source-addons-44e0727c74581f57a8f1fdc0f015f6a92c512a15';
    $pos = mb_strpos($c['dirname'], $ll) + mb_strlen($ll) + 1;
    $d = mb_substr($c['dirname'], $pos);
    $ddd = explode(DIRECTORY_SEPARATOR, $d);

    return $c['extension'] === 'php' && $ddd[0] === 'app';
});
var_dump($a);

/*$info = [];
foreach ($addons as $addon) {
    $info[$addon] = (new Builder($client, 'airslateinc', $addon))
        ->collect(function (FileProcessor $file) {
            return $file
                ->take('composer.lock')
                ->findInJson('airslate/addon-php-utils');
        });
}

$v = [];
foreach ($info as $key => $value) {
    $v[$value][] = $key;
}

var_dump($v);*/
