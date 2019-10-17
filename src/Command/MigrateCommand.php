<?php

namespace App\Command;

use Doctrine\Bundle\MigrationsBundle\Command\MigrationsMigrateDoctrineCommand;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;

class MigrateCommand extends AbstractMultiDataBaseCommand
{
    const DOCTRINE_MIGRATION_COMMAND = 'doctrine:migrations:migrate';
    const DOCTRINE_QUIET_FLAG = '-q';

    protected function configure(): void
    {
        $this->setName('app:migrate')
            ->setDescription('Migrate all databases with one command')
            ->addArgument('version', InputArgument::OPTIONAL);
    }

    public function performActions(): void
    {
        $doctrineApplication = $this->createNewConsoleApplication();
        $doctrineApplication->setAutoExit(false);
        $doctrineApplication->run($this->getDoctrineInput());
    }

    private function createNewConsoleApplication(): Application
    {
        $application = new Application($this->kernel);
        $application->setCatchExceptions(true);
        $application->setHelperSet($this->createHelperSet());
        ConsoleRunner::addCommands($application);
        $application->addCommands([$this->getMigrationCommand()]);

        return $application;
    }

    private function createHelperSet(): HelperSet
    {
        $helperSet = ConsoleRunner::createHelperSet($this->getEntityManager());
        $helperSet->set(new QuestionHelper());

        return $helperSet;
    }

    private function getMigrationCommand(): MigrationsMigrateDoctrineCommand
    {
        $doctrineMigrateCommand = new MigrationsMigrateDoctrineCommand();
        $doctrineMigrateCommand->setMigrationConfiguration(
            $this->getDoctrineMigrationConfiguration()
        );

        return $doctrineMigrateCommand;
    }

    private function getDoctrineMigrationConfiguration(): Configuration
    {
        $connection = $this->getEntityManager()->getConnection();
        $migrationConfiguration = new Configuration($connection);
        $migrationConfiguration->setName(ucfirst($connection->getDatabase()) . ' database');

        return $migrationConfiguration;
    }


    private function getDoctrineInput(): ArgvInput
    {
        return new ArgvInput([
            'application',
            self::DOCTRINE_MIGRATION_COMMAND,
            self::DOCTRINE_QUIET_FLAG,
        ]);
    }
}
