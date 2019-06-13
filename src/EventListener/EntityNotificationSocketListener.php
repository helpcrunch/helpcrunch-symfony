<?php

namespace Helpcrunch\EventListener;

use Helpcrunch\Entity\HelpcrunchEntity;
use Helpcrunch\Service\SocketService;
use Helpcrunch\Traits\HelpcrunchServicesTrait;

class EntityNotificationSocketListener
{
    use HelpcrunchServicesTrait;

    /**
     * @var SocketService
     */
    private $socketService;

    /**
     * @var array
     */
    private $changesSet = [];

    public function __construct(SocketService $socketService)
    {
        $this->socketService = $socketService;
    }

    public function postPersist(HelpcrunchEntity $entity): void
    {
        $this->prepareAndSendEvent($entity);
    }

    public function preUpdate(HelpcrunchEntity $entity): void
    {
        $unitOfWork = $this->getEntityManager()->getUnitOfWork();
        $updatedFields = $unitOfWork->getEntityChangeSet($entity);

        $this->changesSet = array_keys($updatedFields);
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

    private function prepareEventData(HelpcrunchEntity $entity): array
    {
        $eventData['event_id'] = $this->generateEventId($entity) . '.' .
            $this->getEventActionForEventId();
        $eventData['event_data'] = empty($this->changesSet)
            ? $entity->jsonSerialize()
            : $this->getUpdatedEntityFields($entity);

        return $eventData;
    }

    private function generateEventId(HelpcrunchEntity $entity): string
    {
        return $this->getEntityName($entity) .
            '[' . $_SERVER['SERVER_NAME'] . ']' .
            '[' . $entity->id . ']';
    }

    private function getEventActionForEventId(): string
    {
        return empty($this->changesSet) ? 'added' : 'changed';
    }

    private function getEntityName(HelpcrunchEntity $entity): string
    {
        $entityName = explode('\\', get_class($entity));
        $entityName = array_pop($entityName);

        return lcfirst($entityName);
    }

    private function getUpdatedEntityFields(HelpcrunchEntity $entity): array
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
}
