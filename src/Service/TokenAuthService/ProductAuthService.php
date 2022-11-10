<?php

namespace Helpcrunch\Service\TokenAuthService;

use Helpcrunch\Service\AbstractTokenAuthService;

class ProductAuthService extends AbstractTokenAuthService
{
    const DEVICE_TOKEN_LENGTH = 64;

    /**
     * @var int
     */
    private $productId;

    public function isTokenValid(): bool
    {
        return true;
    }

    protected function generateToken(): string
    {
        return base64_encode(openssl_random_pseudo_bytes(self::DEVICE_TOKEN_LENGTH));
    }

    public function setProductId(int $productId): self
    {
        $this->productId = $productId;

        return $this;
    }
}
