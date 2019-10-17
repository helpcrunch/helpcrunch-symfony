<?php

namespace App\Command;

use App\Helper\DatabaseHelper;
use Helpcrunch\Helper\SQLExecutor;

abstract class AbstractMultiDataBaseCommand extends AbstractCommand
{
    /**
     * @var int
     */
    protected $dataBaseCount;

    /**
     * @var array
     */
    protected $dataBaseList;

    public function runCommand(): void
    {
        $this->setProgressBar($this->dataBaseCount);
        $this->getProgressBar()->start();
        $SQLExecutor = new SQLExecutor($this->getDBConnectionService()->getConnection());
        foreach ($this->dataBaseList as $dataBaseName) {
            if (!$SQLExecutor->checkIfDatabaseExists($dataBaseName)) {
                continue;
            }
            if ($dataBaseName != 'helpcrunch_copy') {
                continue;
            }
            $this->changeDataBase($dataBaseName);
            $this->performActions();
            $this->getProgressBar()->advance();
        }
        $this->getProgressBar()->finish();
        $this->output->writeln('');
    }

    protected function init(): void
    {
        parent::init();
        $this->setDataBasesList();
        $this->output->writeln('Executing ' . $this->getName() . ' for ' . $this->dataBaseCount . ' databases');
    }

    private function setDataBasesList(): void
    {
        $allDatabases = $this->getEntityManager()
            ->getConnection()
            ->getSchemaManager()
            ->listDatabases();
        foreach ($allDatabases as $key => $databaseName) {
            if (DatabaseHelper::isKnowledgeBaseDB($databaseName)) {
                unset($allDatabases[$key]);
            }
        }

        $this->dataBaseList = $allDatabases;
        $this->dataBaseCount = count($allDatabases);
    }
}
