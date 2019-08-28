<?php

namespace Helpcrunch\EventListener;

use Exception;
use Helpcrunch\Helper\SentryHelper;
use Psr\Log\LoggerInterface;
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
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Exception|null
     */
    protected $exception = null;

    /**
     * @var Kernel|null
     */
    protected $kernel = null;

    public function __construct(ContainerInterface $container, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->logger = $logger;
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

        $this->log();
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

    private function log(): void
    {
        $log = [
            'code' => $this->exception->getCode(),
            'message' => $this->exception->getMessage(),
            'called' => [
                'file' => $this->exception->getTrace()[0]['file'] ?? 'Undefined',
                'line' => $this->exception->getTrace()[0]['line'] ?? 'Undefined',
            ],
            'occurred' => [
                'file' => $this->exception->getFile(),
                'line' => $this->exception->getLine(),
            ],
        ];

        if ($this->exception->getPrevious() instanceof Exception) {
            $log += [
                'previous' => [
                    'message' => $this->exception->getPrevious()->getMessage(),
                    'exception' => get_class($this->exception->getPrevious()),
                    'file' => $this->exception->getPrevious()->getFile(),
                    'line' => $this->exception->getPrevious()->getLine(),
                ],
            ];
        }

        $this->logger->error(json_encode($log));
    }

    abstract protected function processException(Exception $exception, ExceptionEvent $event): void;
}
