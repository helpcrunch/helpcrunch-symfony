<?php

namespace Helpcrunch\Service\TokenAuthService;

use Helpcrunch\Service\AbstractTokenAuthService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InternalAppAuthService extends AbstractTokenAuthService
{
    const INTERNAL_APP_TOKEN_KEY = 'internal_app_token';
    const TOKEN_LENGTH = 30;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    public function isTokenValid(): bool
    {
        return $this->token === $this->getRedisService()->getData(self::INTERNAL_APP_TOKEN_KEY);
    }

    public function getInternalAppToken()
    {
        return $this->getRedisService()->getData(self::INTERNAL_APP_TOKEN_KEY);
    }

    protected function generateToken(): string
    {
        return bin2hex(openssl_random_pseudo_bytes(self::TOKEN_LENGTH));
    }
}
