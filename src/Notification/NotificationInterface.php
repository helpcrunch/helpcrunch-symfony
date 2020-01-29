<?php

namespace Helpcrunch\Notification;

interface NotificationInterface
{
    public function getPayload(): array;

    public function setNotificationId(string $notificationId): self;

    public function setNotificationData(array $notificationData): self;
}
