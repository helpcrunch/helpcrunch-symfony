<?php

namespace Helpcrunch\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\EntityManagerInterface;
use Helpcrunch\Helper\SQLExecutor;
use Helpcrunch\Traits\HelpcrunchServicesTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DBConnectionService
{
    use HelpcrunchServicesTrait;

    const HELPCRUNCH_DOMAIN = 'helpcrunch';

    /**
     * @var Connection $connection
     */
    protected $connection;

    /**
     * @var EntityManagerInterface $entityManager
     */
    protected $entityManager;

    /**
     * @var ContainerInterface $container
     */
    protected $container;

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
