<?php

namespace Helpcrunch\Service;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class ApiRequestService
{
    const DEFAULT_DOMAIN = 'api';
    const ENDPOINTS_PREFIX = '/api';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $schema;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var
     */
    private $key;

    public function __construct(string $key, string $schema, string $domain)
    {
        $this->key = $key;
        $this->schema = $schema;
        $this->domain = $domain;

        $this->client = new Client(['headers' => $this->setHeaders()]);
    }

    public function post(string $organizationDomain, string $endpoint, array $data = []): ResponseInterface
    {
        return $this->makeRequest('post', $organizationDomain, $endpoint, [
            RequestOptions::JSON => $data
        ]);
    }

    public function makeRequest(
        string $method,
        string $organizationDomain,
        string $endpoint,
        array $options = []
    ): ResponseInterface {
        $response = $this->client->request($method, $this->getUrl($organizationDomain, $endpoint), $options);

        return $response;
    }

    private function getUrl(string $organizationDomain, string $endpoint): string
    {
        return $this->schema . $organizationDomain . '.' . $this->domain . self::ENDPOINTS_PREFIX . $endpoint;
    }

    private function setHeaders(): array
    {
        return [
            'Authorization' => 'Bearer helpcrunch-service="' . $this->key . '"',
            'Content-Type' => 'application/json'
        ];
    }
}
