<?php

namespace Helpcrunch\Service;

use Helpcrunch\Service\NotificationService\AbstractNotificationService;
use Helpcrunch\Service\SocketConfigurationService\PubSubSocketConfigurationService;

class PubSubNotificationService extends AbstractNotificationService
{
    public function __construct(PubSubSocketConfigurationService $pubSubSocketConfigurationService)
    {
        parent::__construct($pubSubSocketConfigurationService);
    }
}
