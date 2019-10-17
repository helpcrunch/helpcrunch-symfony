<?php

namespace Helpcrunch\Command;

use App\Helper\DatabaseHelper;
use Symfony\Component\Console\Input\InputArgument;

abstract class AbstractSingleDataBaseCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected static $defaultDatabase;

    protected function configure()
    {
        $this->addArgument(
            'database',
            null,
            InputArgument::REQUIRED,
            static::$defaultDatabase
        );
    }

    public function runCommand(): void
    {
        $this->chooseDataBase();
        $this->performActions();
    }

    abstract protected function chooseDataBase(): void;
}
