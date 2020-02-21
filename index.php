<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

$client = new \GuzzleHttp\Client();
$a = $client->request('GET', 'http:/v1.40/containers/json', [
    'curl' => [
        CURLOPT_UNIX_SOCKET_PATH => '/var/run/docker.sock'
    ]
]);

$aa = json_decode($a->getBody()->getContents(), true);
foreach ($aa as $aaa) {
    echo $aaa['Names'][0] . ' ' . $aaa['State'] . "\n";
    var_dump($aaa);
    die;
}
