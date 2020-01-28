<?php

namespace Helpcrunch\Service\SocketConfigurationService;

use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractSocketConfigurationService
{
    public const SOCKET_DSN_FIELD = 'dsn';

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    abstract public function getConfiguration(): array;
}
