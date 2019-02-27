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
     * @var string
     */
    private $key;

    public function __construct(string $key, string $schema, string $domain)
    {
        $this->key = $key;
        $this->schema = $schema;
        $this->domain = $domain;

        $this->client = new Client(['headers' => $this->setHeaders()]);
    }

    /**
     * @param string $domain
     * @param string $endpoint
     * @param array $options
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $domain, string $endpoint, array $options = [])
    {
        return $this->makeRequest('get', $domain, $endpoint, $options);
    }

    /**
     * @param string $organizationDomain
     * @param string $endpoint
     * @param array $data
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(string $organizationDomain, string $endpoint, array $data = []): ResponseInterface
    {
        return $this->makeRequest('post', $organizationDomain, $endpoint, [
            RequestOptions::JSON => $data
        ]);
    }

    /**
     * @param string $organizationDomain
     * @param string $endpoint
     * @param array $data
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function put(string $organizationDomain, string $endpoint, array $data = []): ResponseInterface
    {
        return $this->makeRequest('put', $organizationDomain, $endpoint, [
            RequestOptions::JSON => $data
        ]);
    }

    /**
     * @param string $organizationDomain
     * @param string $endpoint
     * @param array $data
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(string $organizationDomain, string $endpoint, array $data = []): ResponseInterface
    {
        return $this->makeRequest('delete', $organizationDomain, $endpoint, [
            RequestOptions::JSON => $data
        ]);
    }

    /**
     * @param string $method
     * @param string $organizationDomain
     * @param string $endpoint
     * @param array $options
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function makeRequest(
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