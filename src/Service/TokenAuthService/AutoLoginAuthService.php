<?php

namespace Helpcrunch\Service\TokenAuthService;

use Helpcrunch\Service\AbstractTokenAuthService;

class AutoLoginAuthService extends AbstractTokenAuthService
{
    const AUTO_LOGIN_TOKEN_TTL = '18000';

    public function isTokenValid(): bool
    {
        if (!$this->token) {
            return false;
        }

        return (bool) $this->getRedisService()->getData($this->getTokenKey());
    }

    protected function generateToken(): string
    {
        return md5(rand(1000, 9999) . time());
    }

    protected function writeTokenToRedis($id): string
    {
        $token = $this->getTokenKey();

        $this->getRedisService()->pushData($token, $id, self::AUTO_LOGIN_TOKEN_TTL);

        return $token;
    }
}
