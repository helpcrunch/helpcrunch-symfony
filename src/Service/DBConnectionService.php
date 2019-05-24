<?php

namespace Helpcrunch\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\EntityManagerInterface;
use Helpcrunch\Helper\SQLExecutor;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DBConnectionService
{
    const HELPCRUNCH_DOMAIN = 'helpcrunch';

    /**
     * @var Connection $connection
     */
    private $connection;

    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @var ContainerInterface $container
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->entityManager = $this->getEntityManager();
    }

    public function close(): void
    {
        $this->connection->close();
    }

    public function createDatabase(string $dataBaseName): void
    {
        try {
            $this->connection->query('CREATE DATABASE "' . $dataBaseName . '"');
        } catch (DriverException $exception) {
            SQLExecutor::checkException($exception);
        }
    }
}
