<?php

namespace Helpcrunch;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerHelper
{
    /**
     * @var ContainerInterface|null
     */
    private static $container = null;

    /**
     * @param ContainerInterface $container
     */
    public static function setContainer(ContainerInterface $container): void
    {
        self::$container = $container;
    }

    /**
     * @return null|ContainerInterface
     */
    public static function getContainer()
    {
        return self::$container;
    }
}
