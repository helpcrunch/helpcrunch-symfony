<?php

namespace Helpcrunch\Service\SocketConfigurationService;

use InvalidArgumentException;

class PubSubSocketConfigurationService extends AbstractSocketConfigurationService
{
    public function getConfiguration(): array
    {
        $dsn = $this->container->getParameter('pubsub_socket_connection');
        if (empty($dsn)) {
            throw new InvalidArgumentException('Missing socket configuration');
        }

        return [
            self::SOCKET_DSN_FIELD => $dsn,
        ];
    }
}
