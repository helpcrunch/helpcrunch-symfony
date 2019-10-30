<?php

namespace Helpcrunch\EventListener;

use Helpcrunch\Service\SocketService;
use Helpcrunch\Traits\HelpcrunchServicesTrait;
use Helpcrunch\Entity\HelpcrunchEntity;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractEntitySocketNotificationListener
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

    /**
     * @var array
     */
    protected $changesSet = [];

    public function __construct(ContainerInterface $container, SocketService $socketService)
    {
        $this->container = $container;
        $this->socketService = $socketService;
    }

    public function postPersist(HelpcrunchEntity $entity): void
    {
        if ($this->checkEntityTypeIsCorrect($entity)) {
            $this->prepareAndSendEvent($entity);
        }
    }

    public function preUpdate(HelpcrunchEntity $entity): void
    {
        if ($this->checkEntityTypeIsCorrect($entity)) {
            $unitOfWork = $this->getEntityManager()->getUnitOfWork();
            $updatedFields = $unitOfWork->getEntityChangeSet($entity);

            $this->changesSet = array_keys($updatedFields);
        }
    }

    public function postUpdate(HelpcrunchEntity $entity): void
    {
        if (!empty($this->changesSet)) {
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

    protected function prepareEventData(HelpcrunchEntity $entity): array
    {
        $eventId = $this->generateEventId($entity);
        if (!$eventId) {
            return [];
        }

        if (empty($this->changesSet)) {
            $entityData = $entity->jsonSerialize();
        } else {
            $entityData = $this->getUpdatedEntityFields($entity);
        }

        $eventData['event_id'] = $eventId;
        $eventData['event_data'] = $entityData;

        return $eventData;
    }

    protected function getUpdatedEntityFields(HelpcrunchEntity $entity): array
    {
        $data = [];
        foreach ($this->changesSet as $field) {
            $data[$field] = $entity->$field;
        }

        return $data;
    }

    private function sendEventNotification(array $data): void
    {
        $this->socketService->sendEvent($data);
    }

    abstract protected function checkEntityTypeIsCorrect(HelpcrunchEntity $entity): bool;

    abstract protected function generateEventId(HelpcrunchEntity $entity);
}
