<?php
declare(strict_types=1);

use AirSlate\Releaser\Builder;
use AirSlate\Releaser\Processors\FileProcessor;
use AirSlate\Releaser\Processors\YamlProcessor;
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

$a = $client->contents()->readFile('airslateinc', 'prefill-from-source-addons', 'docker/config');

var_dump($a);

