<?php

namespace Helpcrunch\Service\TokenAuthService;

use BadMethodCallException;
use Helpcrunch\Auth\AuthInterface;
use Helpcrunch\Auth\Exceptions\InvalidTokenException;
use Helpcrunch\Service\AbstractTokenAuthService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class JWTAuthService
 *
 * @package Helpcrunch\Service\TokenAuthService
 */
class JWTAuthService extends AbstractTokenAuthService
{
    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * JWTAuthService constructor.
     *
     */
    public function __construct(ContainerInterface $container, RequestStack $request = null)
    {
        $this->auth = $container->get(AuthInterface::class);
        parent::__construct($container, $request);
    }

    /**
     * Checks if token is valid
     */
    public function isTokenValid(): bool
    {
        try {
            $parsedToken = $this->auth->parse($this->token);

            return $parsedToken->validate();
        } catch (InvalidTokenException $exception) {
            return false;
        }
    }

    /**
     * Generates the token
     */
    public function generateToken(): string
    {
        throw new BadMethodCallException('Cannot generate the token for this auth provider.');
    }
}
