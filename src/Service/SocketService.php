<?php

namespace Helpcrunch\Service;

use Predis\ClientInterface;

class SocketService
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * SocketService constructor.
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Sends data to redis pub/sub
     */
    public function sendEvent(array $data): void
    {
        $this->client->publish(
            $data['event_id'],
            !empty($data['event_data']) ?
                json_encode($data['event_data']) :
                null
        );
    }
}
