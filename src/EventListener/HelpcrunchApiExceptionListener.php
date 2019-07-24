<?php

namespace Helpcrunch\EventListener;

use Exception;
use Helpcrunch\Helper\SentryHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Kernel;

abstract class HelpcrunchApiExceptionListener
{
    const DEFAULT_EXCEPTION_ERROR_MESSAGE = 'Server error';

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Exception|null
     */
    protected $exception = null;

    /**
     * @var Kernel|null
     */
    protected $kernel = null;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->getKernel();
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $this->getException($event);
        if ($this->kernel->isConsoleApplication()) {
            SentryHelper::logException($this->exception);

            return;
        }

        if ($this->kernel->isProd()) {
            SentryHelper::logException($this->exception);
        }

        $this->processException($this->exception, $event);
    }

    protected function getException(ExceptionEvent $event): void
    {
        if (!$this->exception) {
            $this->exception = $event->getException();
        }
    }

    protected function getKernel(): void
    {
        if (!$this->kernel) {
            $this->kernel = $this->container->get('kernel');
        }
    }

    abstract protected function processException(Exception $exception, ExceptionEvent $event): void;
}
