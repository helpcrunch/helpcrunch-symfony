<?php

namespace Helpcrunch\Service;

use Helpcrunch\Traits\HelpcrunchServicesTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractTokenAuthService
{
    use HelpcrunchServicesTrait;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Request|null
     */
    protected $request = null;

    /**
     * @var string|null
     */
    protected $token = null;

    public function __construct(ContainerInterface $container, RequestStack $request = null)
    {
        $this->container = $container;
        $this->request = $request ? $request->getCurrentRequest() : null;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @param string|int $id
     * @return string
     */
    public function createToken($id): string
    {
        $token = $this->writeTokenToRedis($id);

        return $token;
    }

    /**
     * @return bool|string
     */
    protected function getTokenKey()
    {
        return ($this->getOrganizationsDomain() . '_' . $this->token) ?? $this->generateToken();
    }

    /**
     * @return bool|string
     */
    protected function getOrganizationsDomain()
    {
        if (!$this->request) {
            return false;
        }

        $organizationHeader = $this->request->headers->get('Authorization-Domain', null);
        if ($organizationHeader) {
            return $organizationHeader;
        }

        $hostParts = explode('.', $this->request->getHost());

        return reset($hostParts);
    }

    protected function writeTokenToRedis($id): string
    {
        $token = $this->getTokenKey();

        $this->getRedisService()->pushData($token, $id);

        return $token;
    }

    abstract public function isTokenValid(): bool;

    abstract protected function generateToken(): string;
}
