<?php
declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

use AirSlate\Releaser\Client;

$client = new Client([
    'owner' => 'rostyslavkhaniukov',
    'repo' => 'addon-release-tool',
    'token' => '17b7a4f5e1408effafa7a48e6277745eefb150d9',
]);

try {
    $webhooks = $client->labels()->all('addon-release-tool');
} catch (DomainException $exception) {
    var_dump($exception->getMessage());
}
var_dump($webhooks);
