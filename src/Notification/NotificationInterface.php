<?php

namespace Helpcrunch\Notification;

interface NotificationInterface
{
    public function send(): void;

    public function getPayload(): array;
}
