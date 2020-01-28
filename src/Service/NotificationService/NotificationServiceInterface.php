<?php

namespace Helpcrunch\Service\NotificationService;

interface NotificationServiceInterface
{
    public function sendEvent(array $notificationData): void;
}
