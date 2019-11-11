<?php

declare(strict_types=1);

namespace AirSlate\Releaser;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

/**
 * @package AirSlate\Releaser
 */
class CircleCIEnvPatcher
{
    /** @var string */
    private const CIRCLE_CI_URI = 'https://circleci.com/api/v1.1/project/gh/';

    /** @var Client */
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => static::CIRCLE_CI_URI . getenv('OWNER') . '/',
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
            ],
            RequestOptions::QUERY => [
                'circle-token' => getenv('CIRCLECI_ACCESS_TOKEN'),
            ],
        ]);
    }

    public function process(string $project, array $varList): void
    {
        $variables = $this->getVariables($project);
        foreach ($varList as $variable => $value) {
            if (!$this->checkVariable($variables, $variable)) {
                $response = $this->client->post("{$project}/envvar", [
                    RequestOptions::JSON => [
                        'name' => $variable,
                        'value' => $value,
                    ],
                ]);

                if ($response->getStatusCode() === 201) {
                    echo "Env variable {$variable} added to {$project}\n";
                } else {
                    echo "Something went wrong during creating {$variable} variable in {$project}\n";
                }
            }
        }
    }

    /**
     * @param string $project
     * @return array
     */
    private function getVariables(string $project): array
    {
        $response = $this->client->get("{$project}/envvar");
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param array $vars
     * @param string $key
     * @return bool
     */
    private function checkVariable(array $vars, string $key): bool
    {
        return count(array_filter($vars, function (array $variable) use ($key) {
            return $variable['name'] === $key;
        })) > 0;
    }
}
