<?php

namespace Helpcrunch\Command;

use Doctrine\ORM\EntityManager;
use Helpcrunch\Traits\HelpcrunchServicesTrait;
use ReflectionObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractCommand extends Command
{
    use LockableTrait;
    use HelpcrunchServicesTrait;

    const QUIET_FLAG = 'quiet';
    const INTERACTIVE_FLAG = 'interactive';

    /**
     * @var ProgressBar
     */
    protected $progressBar;

    /**
     * @var int
     */
    protected $startTime;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var KernelInterface
     */
    protected $kernel;

    public function __construct(ContainerInterface $container, KernelInterface $kernel, string $name = null)
    {
        parent::__construct($name);
        $this->container = $container;
        $this->kernel = $kernel;
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->input = $input;
        $this->output = $output;

        if (!$this->lock()) {
            $this->output->writeln('This command is already running in another process.');
            return;
        }
        $this->init();
        $this->runCommand();
        $this->report();
        $this->release();
    }

    protected function setProgressBar(int $count): self
    {
        $this->progressBar = new ProgressBar(
            self::isQuiet() ? (new NullOutput()) : $this->output,
            $count
        );

        return $this;
    }

    protected function getProgressBar(): ProgressBar
    {
        return $this->progressBar;
    }

    protected function init(): void
    {
        $this->startTime = time();
    }

    protected function report(): void
    {
        $seconds = time() - $this->startTime;
        $period = $seconds ? ($seconds . ' seconds') : 'less than one second';
        $this->output->writeln('It took ' . $period . '.');
    }

    public static function isQuiet(): bool
    {
        return in_array(self::QUIET_FLAG, $_SERVER['argv'] ?? []);
    }

    protected function changeDataBase(string $dataBaseName): void
    {
        $connection = $this->getDBConnectionService()->getConnection($dataBaseName);
        $entityManager = EntityManager::create(
            $connection,
            $this->getEntityManager()->getConfiguration(),
            $this->getEntityManager()->getEventManager()
        );

        $reflection = new ReflectionObject($this->container);
        $property = $reflection->getProperty('services');
        $property->setAccessible(true);
        $services = $property->getValue($this->container);
        $services['doctrine.dbal.default_connection'] = $connection;
        $services['doctrine.orm.default_entity_manager'] = $entityManager;
        $property->setValue($this->container, $services);
    }

    abstract protected function runCommand(): void;

    abstract protected function performActions(): void;
}
