<?php

namespace Helpcrunch\Event;

use Helpcrunch\Entity\GetterSetterTrait;
use Helpcrunch\Service\AbstractTokenAuthService;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property AbstractTokenAuthService $tokenService
 * @property Response $response
 * @property int $id
 */
class CreateTokenEvent extends Event
{
    use GetterSetterTrait;

    const CREATE_TOKEN_EVENT = 'auth.create_token';

    /**
     * @var AbstractTokenAuthService
     */
    protected $tokenService;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var Response
     */
    protected $response;
}
