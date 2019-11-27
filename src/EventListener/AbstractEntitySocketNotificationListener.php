<?php

namespace Helpcrunch\EventListener;

use Helpcrunch\Entity\HelpcrunchEntity;
use Helpcrunch\Service\EntityFieldsParserService;
use Helpcrunch\Service\SocketService;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractEntitySocketNotificationListener extends AbstractSocketNotificationListener
{
    /**
     * @var EntityFieldsParserService
     */
    private $entityFieldsParserService;

    /**
     * @var array
     */
    protected $changesSet = [];

    public function __construct(
        ContainerInterface $container,
        SocketService $socketService,
        EntityFieldsParserService $entityFieldsParserService
    ) {
        parent::__construct($container, $socketService);

        $this->entityFieldsParserService = $entityFieldsParserService;
    }

    public function preUpdate(HelpcrunchEntity $entity): void
    {
        if ($this->checkSendPersistEventNotification($entity)) {
            $unitOfWork = $this->getEntityManager()->getUnitOfWork();
            $updatedFields = $unitOfWork->getEntityChangeSet($entity);

            $this->changesSet = array_keys($updatedFields);
        }
    }

    protected function checkSendUpdateEventNotification(HelpcrunchEntity $entity): bool
    {
        return !empty($this->changesSet);
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
            $data[$field] = $this->entityFieldsParserService->checkValue($entity->$field);
        }

        return $data;
    }

    protected function sendEventNotification(array $data): void
    {
        parent::sendEventNotification($data);

        $this->resetChangesSet();
    }

    private function resetChangesSet(): void
    {
        $this->changesSet = [];
    }

    /**
     * @param HelpcrunchEntity $entity
     * @return mixed
     */
    abstract protected function generateEventId(HelpcrunchEntity $entity);
}
