<?php

namespace Helpcrunch\Service;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Helpcrunch\Service\TokenAuthService\InternalAppAuthService;
use Psr\Http\Message\ResponseInterface;

abstract class RequestService
{
    const DEFAULT_DOMAIN = 'api';
    const ENDPOINTS_PREFIX = '/api';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $schema;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $key;

    public function __construct(string $schema, string $domain, InternalAppAuthService $internalAppAuthService)
    {
        $this->key = $internalAppAuthService->getInternalAppToken();
        $this->schema = $schema;
        $this->domain = $domain;

        $this->client = new Client(['headers' => $this->setHeaders()]);
    }

    public function getClient(): Client
    {
        return $this->client;
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
    public function patch(string $organizationDomain, string $endpoint, array $data = []): ResponseInterface
    {
        return $this->makeRequest('patch', $organizationDomain, $endpoint, [
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

    public function deleteAsync(string $organizationDomain, string $endpoint, array $data = []): void
    {
        $this->getClient()->deleteAsync($this->getUrl($organizationDomain, $endpoint), [
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
    protected function makeRequest(
        string $method,
        string $organizationDomain,
        string $endpoint,
        array $options = []
    ): ResponseInterface {
        $response = $this->client->request($method, $this->getUrl($organizationDomain, $endpoint), $options);

        return $response;
    }

    protected function getUrl(string $organizationDomain, string $endpoint): string
    {
        return $this->schema . $organizationDomain . '.' . $this->domain . self::ENDPOINTS_PREFIX . $endpoint;
    }

    protected function setHeaders(): array
    {
        return [
            'Authorization' => 'Bearer helpcrunch-service="' . $this->key . '"',
            'Content-Type' => 'application/json'
        ];
    }
}
