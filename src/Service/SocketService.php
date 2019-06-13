<?php

namespace Helpcrunch\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use ZMQ;
use ZMQContext;
use ZMQSocket;

class SocketService
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ZMQSocket
     */
    private $socket;

    /**
     * @var bool
     */
    private $connected = false;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->socket = (new ZMQContext())->getSocket(ZMQ::SOCKET_PUSH);
    }

    public function sendEvent(array $data): void
    {
        if (!$this->connected) {
            $this->socket->connect($this->container->getParameter('knowledge_base_socket_connection'));
            $this->connected = true;
        }

        $this->socket->send(json_encode($data), ZMQ::MODE_DONTWAIT);
    }
}
