<?php

namespace Helpcrunch\EventSubscriber;

use Helpcrunch\Service\TokenValidationService;
use Helpcrunch\Controller\HelpcrunchController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class AuthenticationSubscriber implements EventSubscriberInterface
{
    /**
     * @var TokenValidationService $tokenValidationService
     */
    private $tokenValidationService;

    public function __construct(TokenValidationService $tokenValidationService)
    {
        $this->tokenValidationService = $tokenValidationService;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if (!is_array($controller)) {
            return;
        }

        $controllerClass = $controller[0];
        $controllersInvokedMethod = $controller[1];

        if (($controllerClass instanceof HelpcrunchController) &&
            !in_array($controllersInvokedMethod, $controllerClass::$unauthorizedMethods)) {
            if (!$this->tokenValidationService->validateToken($event->getRequest())) {
                throw new AccessDeniedHttpException('Invalid token');
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::CONTROLLER => 'onKernelController'];
    }
}
