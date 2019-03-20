<?php

namespace Helpcrunch\Service\TokenAuthService;

use Helpcrunch\Service\AbstractTokenAuthService;

class DeviceAuthService extends AbstractTokenAuthService
{
    const DEVICE_TOKEN_LENGTH = 64;

    /**
     * @var int
     */
    private $deviceId;

    public function isTokenValid(): bool
    {
        return ($this->deviceId == $this->getRedisService()->getData($this->getTokenKey()));
    }

    protected function generateToken(): string
    {
        return base64_encode(openssl_random_pseudo_bytes(self::DEVICE_TOKEN_LENGTH));
    }

    public function setDeviceId(int $deviceId): self
    {
        $this->deviceId = $deviceId;

        return $this;
    }
}
