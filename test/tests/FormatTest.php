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
        $result = [];
        foreach ([
            Format\AT::class => [
                ['U10223006'],
                ['u10223006', 'U10223006'],
                ["U 10.22-30\t06", 'U10223006'],
            ],
            Format\BE::class => [
                ['0776091951'],
                ["077-60.91 95\t1", '0776091951'],
                ['1776091972'],
                ['0417710407'],
                ['0627515170'],
            ],
            Format\BG::class => [
                ['203187055'],
                ['20-31.87 055', '203187055'],
                ['301004503'],
                ['8311046307'],
                ['3002779909'],
            ],
            Format\CHE::class => [
                ['CHE105271245', '', 'CHE-105.271.245'],
                ['Che 105.271 245', 'CHE105271245', 'CHE-105.271.245'],
            ],
            Format\CHUID::class => [
                ['105271245', '', '105.271.245'],
                ['105.271.245', '105271245', '105.271.245'],
            ],
            Format\CY::class => [
                ['00532445O'],
                ['10120320L'],
                ['10120320l', '10120320L'],
                ['10-12.0320 l', '10120320L'],
            ],
            Format\CZ::class => [
                ['699000899'],
                ['69-90.00 899', '699000899'],
                ['46505334'],
                ['7103192745'],
                ['640903926'],
                ['395601439'],
                ['630903928'],
                ['27082440'],
            ],
            Format\DE::class => [
                ['111111125'],
                ['11-11.11 125', '111111125'],
            ],
            Format\DK::class => [
                ['88146328'],
                ['88-14.63 28', '88146328'],
            ],
            Format\EE::class => [
                ['100207415'],
                ['10-02.07 415', '100207415'],
            ],
            Format\EL::class => [
                ['999080536'],
                ['99-90.80 536', '999080536'],
                ['040127797'],
            ],
            Format\ES::class => [
                ['B42656587'],
                ['b 42-65.6587', 'B42656587'],
                ['A0011012B'],
                ['A78304516'],
                ['X5910266W'],
            ],
            Format\FI::class => [
                ['09853608'],
                ['09-85.36 08', '09853608'],
                ['01089940'],
            ],
            Format\FR::class => [
                ['57440242469'],
                ['57-44.02 42469', '57440242469'],
                ['55842229254'],
                ['55-84.22  29254', '55842229254'],
                ['00300076965'],
                ['K7399859412'],
                ['4Z123456782'],
            ],
            Format\GB::class => [
                ['GD001'],
                ['gd 001', 'GD001'],
                ['GD499'],
                ['gd-49 9', 'GD499'],
                ['HA500'],
                ['ha 500', 'HA500'],
                ['HA 999', 'HA999'],
                ['422101815'],
                ['42-21.01 815', '422101815'],
                ['434031494'],
                ['930768410'],
            ],
            Format\HR::class => [
                ['25677819890'],
                ['25-67.78 19  890', '25677819890'],
                ['38192148118'],
            ],
            Format\HU::class => [
                ['10597190'],
                ['10-59.71  90', '10597190'],
                ['21376414'],
            ],
            Format\IE::class => [
                ['8Z49289F'],
                ['8 z-49.289  f', '8Z49289F'],
                ['3628739L'],
                ['36-28.739 l', '3628739L'],
                ['3628739UA'],
                ['3.62.87 39-Ua', '3628739UA'],
                ['5343381W'],
                ['6433435OA'],
            ],
            Format\IT::class => [
                ['00000010215'],
                ['00-15.95 60  366', '00159560366'],
            ],
            Format\LT::class => [
                ['213179412'],
                ['24 95.19-515', '249519515'],
                ['100011602119'],
                ['29-00.61-37 13  14', '290061371314'],
                ['210061371310'],
                ['290061371314'],
                ['208640716'],
            ],
            Format\LU::class => [
                ['10000356'],
                ['26-37.52  45', '26375245'],
            ],
            Format\LV::class => [
                ['40103619251'],
                ['40-10.36 19  251', '40103619251'],
                ['40003009497'],
            ],
            Format\MT::class => [
                ['10769208'],
                ['10-76.92  08', '10769208'],
                ['15121333'],
            ],
            Format\NL::class => [
                ['010000446B01'],
                ['01-00.00 446-b-01', '010000446B01'],
                ['NL123456789B13', '123456789B13', 'NL123456789B13'],
                ['nL-12.34-56789 b 13', '123456789B13', 'NL123456789B13'],
                ['822132503B01'],
                ['8221.32.503.b.01', '822132503B01'],
                ['002342672B42'],
            ],
            Format\PL::class => [
                ['5260001246'],
                ['52-60.00 12  46', '5260001246'],
            ],
            Format\PT::class => [
                ['502757191'],
                ['50-27.57  191', '502757191'],
            ],
            Format\RO::class => [
                ['6529540'],
                ['65-29.54  0', '6529540'],
                ['11198699'],
                ['14186770'],
            ],
            Format\SE::class => [
                ['556188840401'],
                ['55-61.88 84  04-01', '556188840401'],
            ],
            Format\SI::class => [
                ['15012557'],
                ['15-01.25  57', '15012557'],
                ['95796550'],
            ],
            Format\SK::class => [
                ['4030000007'],
                ['40-30.00 00  07', '4030000007'],
                ['2021874855'],
                ['20-21.87 48  55', '2021874855'],
            ],
            Format\XI::class => [
                ['422101815'],
                ['42-21.01 81  5', '422101815'],
                ['422101815', '422101815', 'XI422101815'],
                ['925901618'],
                ['GD001'],
                ['HA500'],
            ],
        ] as $class => $cases) {
            foreach ($cases as $case) {
                array_unshift($case, $class);
                $result[] = $case;
            }
        }

        return $result;
    }

    /**
     * @dataProvider provideValidCases
     *
     * @param string $class
     * @param string $vatNumber
     * @param string $expectedShort empty string if the normalized VAT code is the same as $vatNumber
     * @param string $expectedLong empty string if the normalized VAT code is <prefix><short>
     */
    public function testCheckValidSyntax($class, $vatNumber, $expectedShort = '', $expectedLong = '')
    {
        $format = new $class();
        /** @var \VATLib\Format $format */
        $shortVatNumber = $format->formatShort($vatNumber);
        if ($expectedShort === '') {
            $this->assertSame($vatNumber, $shortVatNumber);
        } else {
            $this->assertSame($expectedShort, $shortVatNumber);
        }
        $longVatNumber = $format->convertShortToLongForm($shortVatNumber);
        if ($expectedLong === '') {
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
        $result = [];
        foreach ([
            Format\AT::class => [
                '10223006',
                'U10223005',
                'U1022300',
                'A10223006',
            ],
            Format\BE::class => [
                '776091951',
                '0776091952',
                '07760919',
                '0776091950',
                '1776091973',
            ],
            Format\BG::class => [
                '10100450',
                '30100450234',
            ],
            Format\CY::class => [
                '0053244511',
                '120001390V',
                '7200013V',
            ],
            Format\CZ::class => [
                '4650533',
                '96505338790',
            ],
            Format\DE::class => [
                '011111129',
                '111111124',
                '1234567',
            ],
            Format\DK::class => [
                '88146327',
                '1234567',
            ],
            Format\EE::class => [
                '1002074',
                'A12345678',
            ],
            Format\EL::class => [
                '0401277960',
                '12345679',
            ],
            Format\ES::class => [
                '00011010',
                '0001101230',
                '0001101B',
                '000110123B',
                'K0011010',
                'K001101230',
                'K001101B',
                'K00110123B',
            ],
            Format\FI::class => [
                '09853607',
                '1234567',
            ],
            Format\FR::class => [
                '0030007696A',
                '1234567890',
                '123456789012',
                'A234567890',
                'A23456789012',
            ],
            Format\GB::class => [
                '434031493',
                '12345',
                'GD500',
                'HA100',
                '12345678',
            ],
            Format\HR::class => [
                '3819214811',
                '1234567890A',
                'AA123456789',
            ],
            Format\HU::class => [
                '2137641',
                '1234567A',
            ],
            Format\IE::class => [
                '8Z49389F',
                '1234567',
                '6433435OB',
            ],
            Format\IT::class => [
                '00000000000',
                '00159560365',
                '00000010214',
                '1234567890',
                '00000001234',
                'AA123456789',
            ],
            Format\LT::class => [
                '213179422',
                '21317941',
                '1234567890',
                '1234567890AB',
            ],
            Format\LU::class => [
                '10000355',
                '1234567',
            ],
            Format\LV::class => [
                '1234567890',
                '123456789012',
            ],
            Format\MT::class => [
                '1234567',
                '1234567X',
                '123456789',
            ],
            Format\NL::class => [
                '010000436B01',
                '12345678901',
                '123456789A12',
                '123456789B00',
            ],
            Format\PL::class => [
                '12342678090',
                '1212121212',
            ],
            Format\PT::class => [
                '502757192',
                '12345678',
            ],
            Format\RO::class => [
                '1',
                '123A56',
                '12345678901',
            ],
            Format\SE::class => [
                '556188840400',
                '1234567890',
                '556181140401',
            ],
            Format\SI::class => [
                '15012556',
                '12345670',
                '01234567',
                '1234567',
                '95736220',
            ],
            Format\SK::class => [
                '5407062531',
                '4030000006',
                '123456789',
                '0123456789',
                '4060000007',
            ],
            Format\XI::class => [
                '434031493',
                '12345',
                'GD500',
                'HA100',
                '12345678',
            ],
        ] as $class => $wrongVats) {
            foreach ($wrongVats as $wrongVat) {
                $result[] = [$class, $wrongVat];
            }
        }

        return $result;
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
