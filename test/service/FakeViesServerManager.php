<?php

namespace VATLib\Test\Service;

use RuntimeException;
use Symfony\Component\Process\Process;

class FakeViesServerManager extends EventListener
{
    const PORT = 32345;

    const USE_IPv6 = false;

    /**
     * @var \Symfony\Component\Process\Process|null
     */
    private $process;

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Test\Service\EventListenerBase::doStartTestSuite()
     */
    protected function doStartTestSuite($testSuite)
    {
        $this->startFakeServer();
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Test\Service\EventListenerBase::doStartTest()
     */
    protected function doStartTest($test)
    {
        $this->checkFakeServer($this->process);
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Service\EventListenerBase::doEndTestSuite()
     */
    protected function doEndTestSuite($testSuite)
    {
        $this->stopFakeServer();
    }

    private function startFakeServer()
    {
        if ($this->process !== null) {
            $this->checkFakeServer($this->process);
            return;
        }
        $process = new Process(
            // $commandline / $command
            PHP_VERSION_ID >= 80000 ? [PHP_BINARY, '-S', self::getBindAddressAndPort()] : (escapeshellarg(PHP_BINARY) . ' -S ' . escapeshellarg(self::getBindAddressAndPort())),
            // $cwd
            ML_VAT_TEST_ROOTDIR . '/fake-vies-server',
            // $env
            null,
            // $input
            null,
            // $timeout
            null
        );
        $process->start(/* $callback */null, /* $env */ PHP_VERSION_ID >= 80000 ? [] : null);
        $startTime = microtime(true);
        for (;;) {
            $connection = @fsockopen(self::USE_IPv6 ? '::1' : '127.0.0.1', self::PORT);
            if ($connection !== false) {
                fclose($connection);
                break;
            }
            $deltaTime = microtime(true) - $startTime;
            if ($deltaTime > 2) {
                $process->stop();
                throw new RuntimeException(sprintf("Failed to start the fake VIES server after %.2f seconds", $deltaTime));
            }
            usleep(50000);
        }
        $this->checkFakeServer($process);
        $this->process = $process;
    }

    private function stopFakeServer()
    {
        $process = $this->process;
        if ($process === null) {
            return;
        }
        $this->process = null;
        $this->checkFakeServer($process);
        $process->stop();
    }

    private function checkFakeServer(Process $process)
    {
        if ($process->getStatus() === Process::STATUS_TERMINATED) {
            $msg = $process->getErrorOutput() ?: $process->getOutput();
            $msg = 'The fake VIES server ended prematurely' . ($msg ? "\n{$msg}" : '');

            throw new RuntimeException($msg);
        }
    }

    public static function getRootURL()
    {
        return 'http://' . (self::USE_IPv6 ? '[::1]' : '127.0.0.1') . ':' . self::PORT . '/';
    }

    private static function getBindAddressAndPort()
    {
        return (self::USE_IPv6 ? '[::]' : '0.0.0.0') . ':' . self::PORT;
    }
}
