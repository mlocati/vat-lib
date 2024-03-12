<?php

namespace VATLib\Test\Service;

abstract class TestCase4 extends TestCaseBase
{
    public static function setupBeforeClass()
    {
        static::doSetUpBeforeClass();
    }

    public function setUp()
    {
        static::doSetUp();
    }

    /**
     * @param string $needle
     * @param string $haystack
     * @param string $message
     */
    public static function assertStringNotContainsString($needle, $haystack, $message = '')
    {
        static::assertNotContains($needle, $haystack, $message);
    }

    public static function assertMatchRegExp($pattern, $string, $message = '')
    {
        static::assertRegExp($pattern, $string, $message);
    }

    public static function assertIsBool($actual, $message = '')
    {
        static::assertInternalType('boolean', $actual, $message);
    }

    public static function assertIsString($actual, $message = '')
    {
        static::assertInternalType('string', $actual, $message);
    }

    public static function assertStringContainsString($substring, $string, $message = '')
    {
        return static::assertContains($substring, $string, $message);
    }
}
