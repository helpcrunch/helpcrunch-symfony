<?php

namespace Helpcrunch\Service\NotificationService;

use Helpcrunch\Service\SocketConfigurationService\AbstractSocketConfigurationService;
use ZMQ;
use ZMQSocket;

abstract class AbstractNotificationService implements NotificationServiceInterface
{
    /**
     * @var AbstractSocketConfigurationService
     */
    protected $socketConfigurationService;

    /**
     * @var ZMQSocket
     */
    protected $socket;

    /**
     * @var bool
     */
    protected $connected = false;

    public function __construct(AbstractSocketConfigurationService $configurationService)
    {
        $this->socketConfigurationService = $configurationService;

        $this->connect();
    }

    private function connect(): void
    {
        $socketDSN = $this->socketConfigurationService->getConfiguration();
        if (!$this->connected) {
            $this->socket->connect($socketDSN[AbstractSocketConfigurationService::SOCKET_DSN_FIELD]);
            $this->connected = true;
        }
    }

    public function sendEvent(array $notificationData): void
    {
        $this->socket->send(json_encode($notificationData), ZMQ::MODE_DONTWAIT);
    }
}
