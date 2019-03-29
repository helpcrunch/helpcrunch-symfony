<?php

namespace Helpcrunch\Service\TokenAuthService;

use Helpcrunch\Service\AbstractTokenAuthService;

class OrganizationAuthService extends AbstractTokenAuthService
{
    const TOKEN_LENGTH = 20;

    public function isTokenValid(): bool
    {
        if (!$this->token) {
            return false;
        }

        return (bool) $this->getRedisService()->getData($this->getTokenKey());
    }

    protected function generateToken(): string
    {
        return bin2hex(openssl_random_pseudo_bytes(self::TOKEN_LENGTH));
    }
}
