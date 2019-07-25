<?php

namespace Helpcrunch\Service;

/** Move interface to bundle */
use Helpcrunch\Notification\NotificationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EmailSenderService
{
    /**
     * @var \ZMQSocket $zmqSocket
     */
    private $zmqSocket;

    /**
     * @param ContainerInterface $container
     * @throws \ZMQSocketException
     */
    public function __construct(ContainerInterface $container)
    {
        $this->zmqSocket = (new \ZMQContext())->getSocket(\ZMQ::SOCKET_PUSH);
        $this->zmqSocket->connect($container->getParameter('mailer_url'));
    }

    /**
     * @param NotificationInterface $notification
     * @throws \ZMQSocketException
     */
    public function sendToQueue(NotificationInterface $notification)
    {
        $this->zmqSocket->send(json_encode($notification->getPayload()));
    }
}
