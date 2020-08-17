<?php

namespace Helpcrunch\Service;

use App\Service\ShutdownManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\EntityManagerInterface;
use Helpcrunch\Helper\SQLExecutor;
use Helpcrunch\Traits\HelpcrunchServicesTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DBConnectionService
{
    use HelpcrunchServicesTrait;

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
        if ($this->connection->isConnected()) {
            $this->connection->close();
        }
    }

    public function createDatabase(string $dataBaseName, Connection $connection = null): void
    {
        if (!$connection) {
            $connection = $this->connection;
        }
        try {
            $connection->query('CREATE DATABASE "' . $dataBaseName . '"');
        } catch (DriverException $exception) {
            SQLExecutor::checkException($exception);
        }
    }

    public function getConnection(string $dataBaseName = null): Connection
    {
        $params = [
            'driver' => $this->entityManager->getConnection()->getDriver()->getName(),
            'port' => $this->entityManager->getConnection()->getPort(),
            'user' => $this->entityManager->getConnection()->getUsername(),
            'host' => $this->entityManager->getConnection()->getHost(),
        ];
        if ($dataBaseName) {
            $params['dbname'] = $dataBaseName;
        }
        if ($dataBaseName == 'tribute') {
            $params['host'] = '176.9.7.240';
        }
        if ($password = $this->entityManager->getConnection()->getPassword()) {
            $params['password'] = $password;
        }
        $this->connection = DriverManager::getConnection(
            $params,
            $this->entityManager->getConfiguration(),
            $this->entityManager->getEventManager()
        );

        ShutdownManager::register(function () {
            $this->close();
        });

        return $this->connection;
    }
}
