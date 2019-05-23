<?php

namespace Helpcrunch\Helper;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\DriverException;
use Exception;
use \Throwable;

class SQLExecutor
{
    const SQL_STATE_COLUMN_DOES_NOT_EXIST = 42703;
    const SQL_STATE_COLUMN_EXISTS = 42701;
    const SQL_STATE_DATABASE_EXISTS = '42P04';
    const SQL_STATE_TABLE_DOES_NOT_EXIST = '42P01';
    const SQL_STATE_TABLE_EXISTS = '42P07';
    const IGNORED_SQL_STATES = [
        self::SQL_STATE_COLUMN_DOES_NOT_EXIST,
        self::SQL_STATE_COLUMN_EXISTS,
        self::SQL_STATE_DATABASE_EXISTS,
        self::SQL_STATE_TABLE_DOES_NOT_EXIST,
        self::SQL_STATE_TABLE_EXISTS,
    ];

    /**
     * @var Connection $connection
     */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function dropDatabase(string $database): bool
    {
        if (!empty($this->checkIfDatabaseExists($database))) {
            try {
                $this->connection->prepare('SELECT pg_terminate_backend(pid) 
                    FROM pg_stat_activity 
                    WHERE pid <> pg_backend_pid() AND datname = \'' . $database . '\'
                ')->execute();
                $this->connection->prepare('DROP DATABASE ' . $database)->execute();
            } catch (Exception $exception) {
                return false;
            }
        }

        return true;
    }

    public function checkIfDatabaseExists(string $domain)
    {
        $sql = 'SELECT datname FROM pg_catalog.pg_database where datname = \'' . $domain . '\'';

        return $this->connection->query($sql)->fetchAll();
    }

    public function dropTable(string $tableName): bool
    {
        $sql = 'DROP TABLE IF EXISTS "' . $tableName . '"';

        try {
            $this->connection->prepare($sql)->execute();
        } catch (DriverException $exception) {
            self:: checkException($exception);

            return false;
        }

        return true;
    }

    public function getTablesList()
    {
        return $this->connection
            ->query('SELECT table_name FROM information_schema.tables WHERE table_schema=\'public\'')
            ->fetchAll();
    }

    public static function checkException(Throwable $exception): void
    {
        if (!($exception instanceof DriverException) ||
            !in_array($exception->getSQLState(), self::IGNORED_SQL_STATES)
        ) {
            throw $exception;
        }
    }

    public function getExistingSequences()
    {
        $sql = 'SELECT sequence_name FROM information_schema.sequences';

        return $this->connection->query($sql)->fetchAll();
    }
}
