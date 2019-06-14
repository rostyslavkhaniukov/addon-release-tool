<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use AirSlate\Releaser\Builder;
use AirSlate\Releaser\Client;

$client = new Client([
    'owner' => 'airslateinc',
    'token' => '1cb165f4f3295a288476aa7ae8aa9657c96d1d0a',
]);

$appReviewers = ['rostyslavkhaniukov', 'nazarovivan', 'YShershnov'];
$platformReviewers = ['m1x0n'];


$builder = new Builder($client, 'airslateinc', 'google-spreadsheets-postfinish-addon');

$builder
    ->from('master')
    ->branch('test-branch')
    ->take('Dockerfile')
    ->replace(
        '/(pdffiller\/php7[12]-ubuntu16:v)\d+\.\d+\.\d+/',
        '${1}1.1.2'
    )
    ->put();


/*$ssasda = $client->refs()->updateRef(
    'airslateinc',
    'google-spreadsheets-watcher-addon',
    'heads/test-branch',
    'f50d0685ac5937fb0ce7f24210ee36ccb8e5e0fb'
);*/

/*$ssasda = $client->refs()->all(
    'airslateinc',
    'google-spreadsheets-watcher-addon'
);*/

die;

/*$result = [];
foreach ($reviews as $review) {
    $login = $review->getUser()->login;
    $submittedAt = strtotime($result[$login]->submitted_at ?? '');
    if (!$submittedAt || $submittedAt < strtotime($review->submitted_at)) {
        $result[$login] = $review;
    }
}

foreach ($result as $user => $review) {
    var_dump($user . ' - ' . $review->state);
}*/
