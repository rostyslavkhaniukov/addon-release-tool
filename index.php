<?php
declare(strict_types=1);

use AirSlate\Releaser\Services\SchemesPathsFetcher;
use Fluffy\GithubClient\Client as GithubClient;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

$addons = [
    'packet-delete-addon',
];

$client = new GithubClient([
    'owner' => getenv('OWNER'),
    'token' => getenv('GITHUB_OAUTH_TOKEN'),
]);

try {
    $fetcher = new SchemesPathsFetcher();
    $v = $fetcher->fetch('prefill-from-source-addons');

    var_dump(iterator_to_array($v));
} catch (\Throwable $e) {
    var_dump($e->getMessage());
}
