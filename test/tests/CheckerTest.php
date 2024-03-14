<?php

namespace VATLib\Test;

use VATLib\Checker;
use VATLib\Checker\Result;
use VATLib\Test\Service\TestCase;
use VATLib\Test\Service\ViesClientWrapper;

class CheckerTest extends TestCase
{
    /**
     * @var \VATLib\Checker
     */
    private static $checker;

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Test\Service\TestCaseBase::doSetUpBeforeClass()
     */
    protected static function doSetUpBeforeClass()
    {
        self::$checker = new Checker();
        self::$checker->setViesClient(new ViesClientWrapper());
    }

    /**
     * @return array[]
     */
    public static function provideCases()
    {
        return [
            ['', '', [
                'isValid' => false,
                'isInvalid' => true,
                'getShortVatNumber' => '',
                'getLongVatNumber' => '',
                'isSyntaxValid' => false,
                'hasExceptions' => false,
                '__toString' => '',
            ]],
            ['00159560366', '', [
                'isValid' => null,
                'isInvalid' => null,
                'getShortVatNumber' => '00159560366',
                'getLongVatNumber' => '00159560366',
                'isSyntaxValid' => null,
                'hasExceptions' => false,
                '__toString' => '00159560366',
            ]],
            ['IT00159560366', '', [
                'isValid' => true,
                'isInvalid' => false,
                'getShortVatNumber' => '00159560366',
                'getLongVatNumber' => 'IT00159560366',
                'isSyntaxValid' => true,
                'hasExceptions' => false,
                '__toString' => 'IT00159560366',
            ]],
            ['IT00159560367', '', [
                'isValid' => false,
                'isInvalid' => true,
                'getShortVatNumber' => 'IT00159560367',
                'getLongVatNumber' => 'IT00159560367',
                'isSyntaxValid' => false,
                'hasExceptions' => false,
                '__toString' => 'IT00159560367',
            ]],
            ['IT00159560366', 'it', [
                'isValid' => true,
                'isInvalid' => false,
                'getShortVatNumber' => '00159560366',
                'getLongVatNumber' => 'IT00159560366',
                'isSyntaxValid' => true,
                'hasExceptions' => false,
                '__toString' => '00159560366',
            ]],
            ['IT-00159560366', 'it', [
                'isValid' => true,
                'isInvalid' => false,
                'getShortVatNumber' => '00159560366',
                'getLongVatNumber' => 'IT00159560366',
                'isSyntaxValid' => true,
                'hasExceptions' => false,
                '__toString' => '00159560366',
            ]],
            ['IT00159560366', 'DE', [
                'isValid' => false,
                'isInvalid' => true,
                'getShortVatNumber' => 'IT00159560366',
                'getLongVatNumber' => 'IT00159560366',
                'isSyntaxValid' => false,
                'hasExceptions' => false,
                '__toString' => 'IT00159560366',
            ]],
            ['IT00159560366', '00', [
                'isValid' => null,
                'isInvalid' => null,
                'getShortVatNumber' => 'IT00159560366',
                'getLongVatNumber' => 'IT00159560366',
                'isSyntaxValid' => null,
                'hasExceptions' => false,
                '__toString' => 'IT00159560366',
            ]],
            ['999080536', 'GR', [
                'isValid' => true,
                'isInvalid' => false,
                'getShortVatNumber' => '999080536',
                'getLongVatNumber' => 'EL999080536',
                'isSyntaxValid' => true,
                'hasExceptions' => false,
                '__toString' => '999080536',
            ]],
            ['12345679801', 'IT', [
                'isValid' => false,
                'isInvalid' => true,
                'getShortVatNumber' => '12345679801',
                'getLongVatNumber' => 'IT12345679801',
                'isSyntaxValid' => true,
                'hasExceptions' => false,
                '__toString' => '12345679801',
            ]],
            ['57440242469', 'FR', [
                'isValid' => true,
                'isInvalid' => false,
                'getShortVatNumber' => '57440242469',
                'getLongVatNumber' => 'FR57440242469',
                'isSyntaxValid' => true,
                'hasExceptions' => true,
                '__toString' => '57440242469',
            ]],
        ];
    }

    /**
     * @dataProvider provideCases
     *
     * @param string|mixed $vatNumber
     * @param string|mixed $countryCode
     */
    public function testCase($vatNumber, $countryCode, array $fields)
    {
        $check = self::$checker->check($vatNumber, $countryCode);
        $this->assertInstanceOf(Result::class, $check);
        foreach ($fields as $getter => $expectedValue) {
            $actualValue = $check->{$getter}();
            $this->assertSame($expectedValue, $actualValue, "Result of {$getter}()");
        }
    }
}
