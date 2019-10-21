<?php

namespace Helpcrunch\Service\TokenAuthService;

use Helpcrunch\Service\AbstractTokenAuthService;

class MobileUserAuthService extends AbstractTokenAuthService
{
    const USER_TOKEN_LENGTH = 48;

    /**
     * @var int|null
     */
    protected $userId = null;

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

    public function getUserId(): int
    {
        if (!$this->token) {
            return 0;
        }

        return (int) $this->getRedisService()->getData($this->getTokenKey());
    }
}
