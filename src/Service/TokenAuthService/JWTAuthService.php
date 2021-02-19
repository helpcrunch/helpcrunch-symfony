<?php

namespace Helpcrunch\Service\TokenAuthService;

use BadMethodCallException;
use Helpcrunch\Auth\Exceptions\InvalidTokenException;
use Helpcrunch\Auth\Customers\Payload;
use Helpcrunch\Auth\Customers\PayloadInterface;
use Helpcrunch\Auth\ReaderInterface;
use Helpcrunch\Service\AbstractTokenAuthService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class JWTAuthService extends AbstractTokenAuthService
{
    /** @var ReaderInterface */
    private $reader;

    /** @var PayloadInterface */
    private $payload;

    public function __construct(ReaderInterface $reader, ContainerInterface $container, RequestStack $request = null)
    {
        $this->reader = $reader;
        parent::__construct($container, $request);
    }

    /**
     * Checks if token is valid
     */
    public function isTokenValid(): bool
    {
        try {
            $this->payload = $this->reader->read($this->token, Payload::class);

            return true;
        } catch (InvalidTokenException $exception) {
            return false;
        }
    }

    public function getPayload(): ?PayloadInterface
    {
        return $this->payload;
    }

    public function generateToken(): string
    {
        throw new BadMethodCallException('Cannot generate the token for this auth provider.');
    }
}
