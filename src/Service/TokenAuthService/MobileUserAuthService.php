<?php

namespace Helpcrunch\Service\TokenAuthService;

use Helpcrunch\Service\AbstractTokenAuthService;

class MobileUserAuthService extends AbstractTokenAuthService
{
    const USER_TOKEN_LENGTH = 48;

    public function isTokenValid(): bool
    {
        if (!$this->token) {
            return false;
        }

        return (bool) $this->getRedisService()->getData($this->getTokenKey());
    }

    protected function generateToken(): string
    {
        return strtr(
            base64_encode(openssl_random_pseudo_bytes(self::USER_TOKEN_LENGTH)), ['/' => '-', '+' => '_']
        );
    }
}
