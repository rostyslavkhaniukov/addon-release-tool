<?php

namespace AirSlate\Releaser\Http;

use AirSlate\Releaser\Exceptions\DomainException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;

/**
 * Class Client
 * @package AirSlate\ApiClient\Http
 */
class Client extends \GuzzleHttp\Client
{
    /**
     * @var array
     */
    private $queryParams = [];

    /**
     * @inheritdoc
     * @throws \AirSlate\Releaser\Exceptions\DomainException
     */
    public function request($method, $uri = '', array $options = [])
    {
        try {
            $resolvedOptions = $this->resolveOptions($options);

            $this->clearOptions();

            $response = parent::request($method, $uri, $resolvedOptions);
        } catch (RequestException $exception) {
            $code = $exception->getCode();
            if ($exception->hasResponse()) {
                $code = $exception->getResponse()->getStatusCode();
            }
            throw new DomainException($exception->getMessage(), $code);
        } catch (\Exception $exception) {
            throw new DomainException($exception->getMessage());
        }

        return $response;
    }

    /**
     * @param string $key
     * @param $values
     * @return Client
     */
    public function setQueryParam(string $key, $values): Client
    {
        if (\is_array($values)) {
            $values = implode(',', $values);
        }

        $this->queryParams[$key] = $values;

        return $this;
    }

    /**
     * @param array $params
     * @return Client
     */
    public function setQueryParams(array $params): Client
    {
        $this->queryParams = array_merge($this->queryParams, $params);

        return $this;
    }

    /**
     * @param array $options
     * @return array
     */
    private function resolveOptions(array $options): array
    {
        if (!empty($this->queryParams)) {
            foreach ($this->queryParams as $param => $value) {
                $options[RequestOptions::QUERY][$param] = $value;
            }
        }

        return $options;
    }

    private function clearOptions(): void
    {
        $this->queryParams = [];
    }
}
