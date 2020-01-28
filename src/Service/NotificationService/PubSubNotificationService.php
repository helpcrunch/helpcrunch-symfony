<?php

namespace Helpcrunch\Service;

use Helpcrunch\Service\NotificationService\NotificationServiceInterface;
use Helpcrunch\Service\SocketConfigurationService\AbstractSocketConfigurationService;
use Helpcrunch\Service\SocketConfigurationService\PubSubSocketConfigurationService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use ZMQ;
use ZMQContext;
use ZMQSocket;

class PubSubNotificationService implements NotificationServiceInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var PubSubSocketConfigurationService
     */
    private $pubsubSocketConfigurationService;

    /**
     * @var ZMQSocket
     */
    private $socket;

    /**
     * @var bool
     */
    private $connected = false;

    public function __construct(
        ContainerInterface $container,
        PubSubSocketConfigurationService $pubSubSocketConfigurationService
    ) {
        $this->container = $container;
        $this->pubsubSocketConfigurationService = $pubSubSocketConfigurationService;
        $this->socket = (new ZMQContext())->getSocket(ZMQ::SOCKET_PUSH);
    }

    public function sendEvent(array $data): void
    {
        $socketDSN = $this->pubsubSocketConfigurationService->getConfiguration();
        if (!$this->connected) {
            $this->socket->connect($socketDSN[AbstractSocketConfigurationService::SOCKET_DSN_FIELD]);
            $this->connected = true;
        }

        $this->socket->send(json_encode($data), ZMQ::MODE_DONTWAIT);
    }
}
