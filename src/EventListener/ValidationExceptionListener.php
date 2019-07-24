<?php

namespace Helpcrunch\EventListener;

use Helpcrunch\Exception\ValidationException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ValidationExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getException();
        if (!($exception instanceof ValidationException)) {
            return;
        }

        $event->setResponse($exception->getExceptionsResponse());
    }
}
