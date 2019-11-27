<?php

namespace Helpcrunch\EventListener;

use Helpcrunch\Entity\HelpcrunchEntity;
use Helpcrunch\Service\SocketService;
use Helpcrunch\Traits\HelpcrunchServicesTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractSocketNotificationListener
{
    use HelpcrunchServicesTrait;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var SocketService
     */
    protected $socketService;

    public function __construct(ContainerInterface $container, SocketService $socketService)
    {
        $this->container = $container;
        $this->socketService = $socketService;
    }

    public function postPersist(HelpcrunchEntity $entity): void
    {
        if ($this->checkSendPersistEventNotification($entity)) {
            $this->prepareAndSendEvent($entity);
        }
    }

    public function postUpdate(HelpcrunchEntity $entity): void
    {
        if ($this->checkSendUpdateEventNotification($entity)) {
            $this->prepareAndSendEvent($entity);
        }
    }

    private function prepareAndSendEvent(HelpcrunchEntity $entity): void
    {
        $eventData = $this->prepareEventData($entity);
        if (!empty($eventData)) {
            $this->sendEventNotification($eventData);
        }
    }

    protected function sendEventNotification(array $data): void
    {
        $this->socketService->sendEvent($data);
    }

    abstract protected function checkSendPersistEventNotification(HelpcrunchEntity $entity): bool;

    abstract protected function checkSendUpdateEventNotification(HelpcrunchEntity $entity): bool;

    abstract protected function prepareEventData(HelpcrunchEntity $entity): array;
}
