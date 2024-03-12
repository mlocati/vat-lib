<?php

namespace VATLib\Test\Service;

use PHPUnit\Framework\TestListener;

abstract class EventListener4 extends EventListenerBase implements TestListener
{
    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit_Framework_TestListener::startTestSuite()
     */
    final public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        $this->doStartTestSuite($suite);
    }

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit_Framework_TestListener::endTestSuite()
     */
    final public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        $this->doEndTestSuite($suite);
    }

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit_Framework_TestListener::startTest()
     */
    final public function startTest(\PHPUnit_Framework_Test $test)
    {
        $this->doStartTest($test);
    }

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit_Framework_TestListener::addError()
     */
    final public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time) {}

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit_Framework_TestListener::addFailure()
     */
    final public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time) {}

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit_Framework_TestListener::addSkippedTest()
     */
    final public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time) {}

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit_Framework_TestListener::addIncompleteTest()
     */
    final public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time) {}

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit_Framework_TestListener::addRiskyTest()
     */
    final public function addRiskyTest(\PHPUnit_Framework_Test $test, \Exception $e, $time) {}

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit_Framework_TestListener::endTest()
     */
    final public function endTest(\PHPUnit_Framework_Test $test, $time) {}
}
