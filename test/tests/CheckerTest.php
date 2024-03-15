<?php

namespace VATLib\Test;

use VATLib\Checker;
use VATLib\Checker\Result;
use VATLib\Format;
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
    public static function provideCheckCases()
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
            ['NL002342672B42', '', [
                'isValid' => true,
                'isInvalid' => false,
                'getShortVatNumber' => '002342672B42',
                'getLongVatNumber' => 'NL002342672B42',
                'isSyntaxValid' => true,
                'hasExceptions' => false,
                '__toString' => 'NL002342672B42',
            ]],
        ];
    }

    /**
     * @dataProvider provideCheckCases
     *
     * @param string|mixed $vatNumber
     * @param string|mixed $countryCode
     */
    public function testCheck($vatNumber, $countryCode, array $fields)
    {
        $check = self::$checker->check($vatNumber, $countryCode);
        $this->assertInstanceOf(Result::class, $check);
        foreach ($fields as $getter => $expectedValue) {
            $actualValue = $check->{$getter}();
            $this->assertSame($expectedValue, $actualValue, "Result of {$getter}()");
        }
    }

    /**
     * @return array[]
     */
    public static function provideApplicableFormatsCases()
    {
        return [
            [null, [], false],
            ['', [], false],
            [false, [], false],
            ['0', [], false],
            ['IT00159560366', [
                Format\IT::class,
            ]],
            ['00159560366', [
                Format\FR::class,
                Format\IT::class,
                Format\LV::class,
            ]],
        ];
    }

    /**
     * @dataProvider provideApplicableFormatsCases
     *
     * @param string|mixed $vatNumber
     * @param string[] $expectedClasses
     */
    public function testApplicableFormats($vatNumber, array $expectedClasses, $maybeOthers = true)
    {
        $actualFormats = self::$checker->getApplicableFormats($vatNumber);
        $this->assertIsArray($actualFormats);
        $actualClasses = [];
        foreach ($actualFormats as $actualFormat) {
            $actualClasses[] = get_class($actualFormat);
        }
        if ($maybeOthers) {
            $sameClasses = array_intersect($expectedClasses, $actualClasses);
            sort($sameClasses);
            $this->assertSame($expectedClasses, $sameClasses);
        } else {
            sort($actualClasses);
            $this->assertSame($expectedClasses, $actualClasses);
        }
    }
}
