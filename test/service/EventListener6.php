<?php

namespace VATLib\Test\Service;

use PHPUnit\Framework\TestListener;

abstract class EventListener6 extends EventListenerBase implements TestListener
{
    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit\Framework\TestListener::startTestSuite()
     */
    final public function startTestSuite(\PHPUnit\Framework\TestSuite $suite)
    {
        $this->doStartTestSuite($suite);
    }

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit\Framework\TestListener::endTestSuite()
     */
    final public function endTestSuite(\PHPUnit\Framework\TestSuite $suite)
    {
        $this->doEndTestSuite($suite);
    }

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit\Framework\TestListener::startTest()
     */
    final public function startTest(\PHPUnit\Framework\Test $test)
    {
        $this->doStartTest($test);
    }

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit\Framework\TestListener::addError()
     */
    final public function addError(\PHPUnit\Framework\Test $test, \Exception $e, $time) {}

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit\Framework\TestListener::addFailure()
     */
    final public function addFailure(\PHPUnit\Framework\Test $test, \PHPUnit\Framework\AssertionFailedError $e, $time) {}

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit\Framework\TestListener::addWarning()
     */
    final public function addWarning(\PHPUnit\Framework\Test $test, \PHPUnit\Framework\Warning $e, $time) {}

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit\Framework\TestListener::addSkippedTest()
     */
    final public function addSkippedTest(\PHPUnit\Framework\Test $test, \Exception $e, $time) {}

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit\Framework\TestListener::addIncompleteTest()
     */
    final public function addIncompleteTest(\PHPUnit\Framework\Test $test, \Exception $e, $time) {}

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit\Framework\TestListener::addRiskyTest()
     */
    final public function addRiskyTest(\PHPUnit\Framework\Test $test, \Exception $e, $time) {}

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit\Framework\TestListener::endTest()
     */
    final public function endTest(\PHPUnit\Framework\Test $test, $time) {}
}
