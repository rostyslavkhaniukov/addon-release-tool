<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use AirSlate\Releaser\Client;

$client = new Client([
    'owner' => 'airslateinc',
    'token' => '1cb165f4f3295a288476aa7ae8aa9657c96d1d0a',
]);

$appReviewers = ['rostyslavkhaniukov', 'nazarovivan', 'YShershnov'];
$platformReviewers = ['m1x0n'];

/*$file = $client->contents()->readFile(
    'airslateinc',
    'google-spreadsheets-watcher-addon',
    'Dockerfile'
);
$newContent = base64_encode(str_replace(
    "pdffiller/php71-ubuntu16:v1.0.42",
    "pdffiller/php71-ubuntu16:v1.1.2",
    $file->getDecoded()
));
$a = $client->blobs()->put(
    'airslateinc',
    'google-spreadsheets-watcher-addon',
    $newContent,
    'base64'
);*/

$s = $client->branches()->get('airslateinc', 'google-spreadsheets-watcher-addon', 'master');

/*$ss = $client->refs()->createRef(
    'airslateinc',
    'google-spreadsheets-watcher-addon',
    'refs/heads/test-branch',
    $s->commit->sha
);

$sssss = $client->commits()->get('google-spreadsheets-watcher-addon', $ss->objectSha);

$dd = $client->trees()->createTree('airslateinc',
    'google-spreadsheets-watcher-addon',
    [
        'base_tree' => $sssss->commit->tree->getSha(),
        'tree' => [
            [
                'path' => 'Dockerfile',
                'mode' => '100644',
                'type' => 'blob',
                'sha' => '362013c9e6882a3bebc06c7903f2cb5f7d2137e0',
            ]
        ],
    ]);*/

$ggg = $client->commits()->commit(
    'airslateinc',
    'google-spreadsheets-watcher-addon',
    'f15743fbdbb20659092d10f8c53b8928136940cd',
    [
        $s->commit->sha,
    ]
);

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
