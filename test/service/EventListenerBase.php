<?php

namespace VATLib\Test\Service;

abstract class EventListenerBase
{
    /**
     * @param \PHPUnit\Framework\TestSuite $suite
     */
    protected function doStartTestSuite($suite) {}

    /**
     * @param \PHPUnit\Framework\TestSuite $suite
     */
    protected function doEndTestSuite($suite) {}

    /**
     * @param \PHPUnit\Framework\Test $test
     */
    protected function doStartTest($test) {}
}
