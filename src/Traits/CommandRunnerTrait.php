<?php
namespace Helpcrunch\Traits;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;

trait CommandRunnerTrait
{
    protected function runCommand(KernelInterface $kernel, string $command, array $options): int
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $arguments = ['command' => $command];
        $arguments = array_merge($arguments, $options);

        $input = new ArrayInput($arguments);
        $output = new NullOutput();

        return $application->run($input, $output);
    }
}
