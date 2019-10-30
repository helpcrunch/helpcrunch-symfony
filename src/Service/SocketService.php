<?php

namespace Helpcrunch\Service;

use InvalidArgumentException;
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
        $socketDSN = $this->container->getParameter('socket_connection');
        if (!$socketDSN) {
            throw new InvalidArgumentException('Missing socket configuration');
        }
        if (!$this->connected) {
            $this->socket->connect($socketDSN);
            $this->connected = true;
        }

        $this->socket->send(json_encode($data), ZMQ::MODE_DONTWAIT);
    }
}
