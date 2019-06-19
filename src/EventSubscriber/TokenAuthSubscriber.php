<?php

namespace Helpcrunch\EventSubscriber;

use Helpcrunch\Controller\HelpcrunchController;
use Helpcrunch\Response\ErrorResponse;
use Helpcrunch\Event\CreateTokenEvent;
use Helpcrunch\Annotation\UnauthorizedAction;
use Helpcrunch\Authentication;
use Helpcrunch\Service\AbstractTokenAuthService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Doctrine\Common\Annotations\Reader;
use ReflectionMethod;
use ReflectionObject;
use ReflectionClass;

class TokenAuthSubscriber implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Reader
     */
    private $annotationReader;

    public function __construct(ContainerInterface $container, Reader $reader)
    {
        $this->container = $container;
        $this->annotationReader = $reader;
    }

    /**
     * Basic Symfony event subscriber. Fired before every request, right before controller will be executed
     *
     * @param FilterControllerEvent $event
     * @throws \ErrorException
     * @return JsonResponse|null
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller)) {
            return null;
        }

        $controller = reset($controller);
        $action = $this->getActionName($event);
        Authentication::setContainer($this->container);

        if (!Authentication::authorize($event->getRequest(), $this->getActionsAnnotations($controller, $action)) &&
            !$this->checkUnauthorizedAnnotation($controller, $action)
        ) {
            $event->setController(function () {
                return new ErrorResponse(
                    'Not authorized',
                    ErrorResponse::HTTP_UNAUTHORIZED,
                    ErrorResponse::HTTP_UNAUTHORIZED
                );
            });
        }
    }

    /**
     * Event subscriber, fired when new an entity needs for new token for authorization
     *
     * @param CreateTokenEvent $event
     */
    public function onCreateToken(CreateTokenEvent $event): void
    {
        /** @var AbstractTokenAuthService $tokenService */
        $tokenService = $this->container->get($event->tokenService);

        $token = $tokenService->createToken($event->id);

        $content = json_decode($event->response->getContent(), true);
        $content['token'] = $token;

        $event->response->setContent(json_encode($content));
    }

    private function getActionName(FilterControllerEvent $event)
    {
        if ($event->getRequest()->attributes->get('_controller')) {
            $action = explode('::', $event->getRequest()->attributes->get('_controller'));

            return end($action);
        }

        return false;
    }

    /**
     * @param $controller
     * @param string $action
     * @return array|null|object
     */
    private function checkUnauthorizedAnnotation($controller, string $action)
    {
        $reflectionMethod = $this->createReflectionMethod($controller, $action);

        $unauthorizedAnnotation = $this->annotationReader->getMethodAnnotation(
            $reflectionMethod,
            UnauthorizedAction::class
        );

        return $unauthorizedAnnotation ?? $this->recursiveAnnotationSearching($reflectionMethod, UnauthorizedAction::class);
    }

    private function getActionsAnnotations($controller, string $action): array
    {
        $reflectionMethod = $this->createReflectionMethod($controller, $action);

        if ($annotations = $this->annotationReader->getMethodAnnotations($reflectionMethod)) {
            return $annotations;
        }

        return $this->recursiveAnnotationSearching($reflectionMethod) ?: [];
    }

    private function createReflectionMethod($controller, string $action): ReflectionMethod
    {
        $reflectionObject = new ReflectionObject($controller);

        return $reflectionObject->getMethod($action);
    }

    /**
     * @param ReflectionMethod $reflectionMethod
     * @param string|null $annotation
     * @return array|null|object
     */
    private function recursiveAnnotationSearching(ReflectionMethod $reflectionMethod, string $annotation = null)
    {
        $declaringClass = $reflectionMethod->getDeclaringClass();
        $parentClass = $declaringClass->getParentClass();

        if (!$parentClass
            || !$this->checkIsHelpcrunchController($parentClass)
            || !$parentClass->hasMethod($reflectionMethod->getName())
        ) {
            return null;
        }

        $parentsMethod = $parentClass->getMethod($reflectionMethod->getName());
        if ($parentsMethod && !empty($this->annotationReader->getMethodAnnotations($parentsMethod))) {
            return $annotation
                ? $this->annotationReader->getMethodAnnotation($parentsMethod, $annotation)
                : $this->annotationReader->getMethodAnnotations($parentsMethod);
        } else {
            return $this->recursiveAnnotationSearching($parentsMethod, $annotation);
        }
    }

    private function checkIsHelpcrunchController(ReflectionClass $class): bool
    {
        return $class->isSubclassOf(HelpcrunchController::class)
            || ($class->getName() == HelpcrunchController::class);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
            CreateTokenEvent::CREATE_TOKEN_EVENT => 'onCreateToken',
        ];
    }
}
