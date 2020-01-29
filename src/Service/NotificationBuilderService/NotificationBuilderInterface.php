<?php

namespace Helpcrunch\Service\NotificationBuilderInterface;

use Helpcrunch\Entity\HelpcrunchEntity;
use Helpcrunch\Notification\NotificationInterface;

interface NotificationBuilderInterface
{
    public function build(HelpcrunchEntity $entity): NotificationInterface;
}
