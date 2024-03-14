<?php

namespace VATLib\Test;

use VATLib\Format;
use VATLib\Test\Service\TestCase;

class FormatTest extends TestCase
{
    /**
     * @return array[]
     */
    public static function provideValidCases()
    {
        return [
            [Format\AT::class, 'U10223006'],
            [Format\AT::class, 'u10223006', 'U10223006'],
            [Format\AT::class, "U 10.22-30\t06", 'U10223006'],
            [Format\BE::class, '0776091951'],
            [Format\BE::class, "077-60.91 95\t1", '0776091951'],
            [Format\BE::class, '1776091972'],
            [Format\BG::class, '203187055'],
            [Format\BG::class, '20-31.87 055', '203187055'],
            [Format\CHE::class, 'CHE105271245', null, 'CHE-105.271.245'],
            [Format\CHE::class, 'Che-105.271 245', 'CHE105271245', 'CHE-105.271.245'],
            [Format\CHUID::class, '105271245', null, '105.271.245'],
            [Format\CHUID::class, '105.271.245', '105271245', '105.271.245'],
            [Format\CY::class, '10120320L'],
            [Format\CY::class, '10120320l', '10120320L'],
            [Format\CY::class, '10-12.0320 l', '10120320L'],
            [Format\CZ::class, '699000899'],
            [Format\CZ::class, '69-90.00 899', '699000899'],
            [Format\DE::class, '111111125'],
            [Format\DE::class, '11-11.11 125', '111111125'],
            [Format\DK::class, '88146328'],
            [Format\DK::class, '88-14.63 28', '88146328'],
            [Format\EE::class, '100207415'],
            [Format\EE::class, '10-02.07 415', '100207415'],
            [Format\EL::class, '999080536'],
            [Format\EL::class, '99-90.80 536', '999080536'],
            [Format\ES::class, 'B42656587'],
            [Format\ES::class, 'b 42-65.6587', 'B42656587'],
            [Format\FI::class, '09853608'],
            [Format\FI::class, '09-85.36 08', '09853608'],
            [Format\FR::class, '57440242469'],
            [Format\FR::class, '57-44.02 42469', '57440242469'],
            [Format\FR::class, '55842229254'],
            [Format\FR::class, '55-84.22  29254', '55842229254'],
            [Format\GB::class, 'GD001'],
            [Format\GB::class, 'gd 001', 'GD001'],
            [Format\GB::class, 'GD499'],
            [Format\GB::class, 'gd-49 9', 'GD499'],
            [Format\GB::class, 'HA500'],
            [Format\GB::class, 'ha 500', 'HA500'],
            [Format\GB::class, 'HA 999', 'HA999'],
            [Format\GB::class, '422101815'],
            [Format\GB::class, '42-21.01 815', '422101815'],
            [Format\GB::class, '434031494'],
            [Format\GB::class, '930768410'],
            [Format\HR::class, '25677819890'],
            [Format\HR::class, '25-67.78 19  890', '25677819890'],
            [Format\HU::class, '10597190'],
            [Format\HU::class, '10-59.71  90', '10597190'],
            [Format\HU::class, '21376414'],
            [Format\IE::class, '8Z49289F'],
            [Format\IE::class, '8 z-49.289  f', '8Z49289F'],
            [Format\IE::class, '3628739L'],
            [Format\IE::class, '36-28.739 l', '3628739L'],
            [Format\IE::class, '3628739UA'],
            [Format\IE::class, '3.62.87 39-Ua', '3628739UA'],
            [Format\IT::class, '00000010215'],
            [Format\IT::class, '00-15.95 60  366', '00159560366'],
            [Format\LT::class, '213179412'],
            [Format\LT::class, '24 95.19-515', '249519515'],
            [Format\LT::class, '100011602119'],
            [Format\LT::class, '29-00.61-37 13  14', '290061371314'],
            [Format\LU::class, '10000356'],
            [Format\LU::class, '26-37.52  45', '26375245'],
            [Format\LV::class, '40103619251'],
            [Format\LV::class, '40-10.36 19  251', '40103619251'],
            [Format\MT::class, '10769208'],
            [Format\MT::class, '10-76.92  08', '10769208'],
            [Format\NL::class, '010000446B01'],
            [Format\NL::class, '01-00.00 446-b-01', '010000446B01'],
            [Format\NL::class, 'NL123456789B13', null, 'NL123456789B13'],
            [Format\NL::class, 'nL-12.34-56789 b 13', 'NL123456789B13', 'NL123456789B13'],
            [Format\NL::class, '822132503B01'],
            [Format\NL::class, '8221.32.503.b.01', '822132503B01'],
            [Format\PT::class, '502757191'],
            [Format\PT::class, '50-27.57  191', '502757191'],
            [Format\PL::class, '5260001246'],
            [Format\PL::class, '52-60.00 12  46', '5260001246'],
            [Format\RO::class, '6529540'],
            [Format\RO::class, '65-29.54  0', '6529540'],
            [Format\SE::class, '556188840401'],
            [Format\SE::class, '55-61.88 84  04-01', '556188840401'],
            [Format\SI::class, '15012557'],
            [Format\SI::class, '15-01.25  57', '15012557'],
            [Format\SK::class, '4030000007'],
            [Format\SK::class, '40-30.00 00  07', '4030000007'],
            [Format\SK::class, '2021874855'],
            [Format\SK::class, '20-21.87 48  55', '2021874855'],
            [Format\XI::class, '422101815'],
            [Format\XI::class, '42-21.01 81  5', '422101815'],
        ];
    }

    /**
     * @dataProvider provideValidCases
     *
     * @param string $class
     * @param string $vatNumber
     * @param string|null $expectedShort NULL if the normalized VAT code is the same as $vatNumber
     * @param string|null $expectedLong NULL if the normalized VAT code is <prefix><short>
     */
    public function testCheckValidSyntax($class, $vatNumber, $expectedShort = null, $expectedLong = null)
    {
        $format = new $class();
        /** @var \VATLib\Format $format */
        $shortVatNumber = $format->formatShort($vatNumber);
        if ($expectedShort === null) {
            $this->assertSame($vatNumber, $shortVatNumber);
        } else {
            $this->assertSame($expectedShort, $shortVatNumber);
        }
        $longVatNumber = $format->convertShortToLongForm($shortVatNumber);
        if ($expectedLong === null) {
            $this->assertSame($format->getVatNumberPrefix() . $shortVatNumber, $longVatNumber);
        } else {
            $this->assertSame($expectedLong, $longVatNumber);
        }
        if (in_array($format->getCountryCode(), ['AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK'], true)) {
            $this->assertSame(Format::REGION_EUROPEAN_UNION, $format->getFiscalRegion());
        } else {
            $this->assertSame('', $format->getFiscalRegion());
        }
    }

    /**
     * @return array[]
     */
    public static function provideInvalidCases()
    {
        return [
            [Format\AT::class, '10223006'],
            [Format\AT::class, 'U10223005'],
            [Format\BE::class, '0776091950'],
            [Format\BE::class, '1776091973'],
            [Format\DE::class, '011111129'],
            [Format\IT::class, '00000000000'],
            [Format\IT::class, '00159560365'],
            [Format\SK::class, '5407062531'],
        ];
    }

    /**
     * @dataProvider provideInvalidCases
     *
     * @param string $class
     * @param string $vatNumber
     * @param string $expectedResult true if the VAT code is the same as the ISO 3166 code
     * @param string|true $expectedResult
     */
    public function testCheckInvalidSyntax($class, $vatNumber)
    {
        $format = new $class();
        /** @var \VATLib\Format $format */
        $normalizedVatNumber = $format->formatShort($vatNumber);
        $this->assertSame('', $normalizedVatNumber);
    }
}
