<?php

require_once __DIR__.'/vendor/autoload.php';

use AirSlate\Releaser\Client;

$client = new Client([
    'owner' => 'airslateinc',
    'repo' => 'mysql-prefill-addon',
    'token' => 'token',
]);
