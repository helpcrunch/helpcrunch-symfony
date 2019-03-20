<?php

namespace Helpcrunch\Traits;

use Helpcrunch\Response\ErrorResponse;
use Helpcrunch\Event\CreateTokenEvent;
use Helpcrunch\EventSubscriber\TokenAuthSubscriber;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Response;

trait CreateTokenEventDispatcherTrait
{
    protected function dispatchCreateTokenEvent(Response $response, string $tokenHandlerClass, int $id): void
    {
        if ($response instanceof ErrorResponse) {
            return;
        }

        $event = new CreateTokenEvent();

        $event->tokenService = $tokenHandlerClass;
        $event->id = $id;
        $event->response = $response;

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($this->container->get(TokenAuthSubscriber::class));
        $dispatcher->dispatch(CreateTokenEvent::CREATE_TOKEN_EVENT, $event);
    }
}
