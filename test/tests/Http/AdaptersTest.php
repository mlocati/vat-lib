<?php

namespace VATLib\Test\Http;

use DateInterval;
use DateTimeImmutable;
use VATLib\Exception\IOError;
use VATLib\Http\Adapter;
use VATLib\Test\Service\TestCase;
use VATLib\Test\Service\ViesClientWrapper;
use VATLib\Vies;

class AdaptersTest extends TestCase
{
    public function testGuzzle()
    {
        if (!class_exists(\GuzzleHttp\Client::class)) {
            $this->assertFalse(Adapter\Guzzle::isAvailable());
            return;
        }
        $this->assertTrue(Adapter\Guzzle::isAvailable());
        $adapter = new Adapter\Guzzle();
        $vies = new ViesClientWrapper($adapter);
        $this->assertSame($adapter, $vies->getHttpAdapter());
        $this->checkCommunication($vies);
    }

    public function testStream()
    {
        if (!in_array('http', stream_get_wrappers(), true)) {
            $this->assertFalse(Adapter\Stream::isAvailable());
            return;
        }
        $this->assertTrue(Adapter\Stream::isAvailable());
        $adapter = new Adapter\Stream();
        $vies = new ViesClientWrapper($adapter);
        $this->assertSame($adapter, $vies->getHttpAdapter());
        $this->checkCommunication($vies);
    }

    public function testZend()
    {
        if (!class_exists(\Zend\Http\Client::class)) {
            $this->assertFalse(Adapter\Zend::isAvailable());
            return;
        }
        $this->assertTrue(Adapter\Zend::isAvailable());
        $adapter = new Adapter\Zend();
        $vies = new ViesClientWrapper($adapter);
        $this->assertSame($adapter, $vies->getHttpAdapter());
        $this->checkCommunication($vies);
    }

    public function testCurl()
    {
        if (!function_exists('curl_init')) {
            $this->assertFalse(Adapter\Curl::isAvailable());
            return;
        }
        $this->assertTrue(Adapter\Curl::isAvailable());
        $adapter = new Adapter\Curl();
        $vies = new ViesClientWrapper($adapter);
        $this->assertSame($adapter, $vies->getHttpAdapter());
        $this->checkCommunication($vies);
    }

    private function checkCommunication(ViesClientWrapper $vies)
    {
        $this->checkStatus($vies);
        $this->checkGoodVatNumber($vies);
        $this->checkWrongVatNumber($vies);
        $this->checkWrongCountryCode($vies);
    }

    private function checkStatus(ViesClientWrapper $vies)
    {
        $status = $vies->checkStatus();
        $this->assertInstanceOf(Vies\CheckStatus\Response::class, $status);
        $vowStatus = $status->getVowStatus();
        $this->assertInstanceOf(Vies\CheckStatus\Response\VowStatus::class, $vowStatus);
        $this->assertIsBool($vowStatus->isAvailable());
        $this->assertNotContains('12', $status->getCountryCodes());
        $this->assertNull($status->getCountryStatus('12'));
        $countryCodes = $status->getCountryCodes();
        $this->assertContains('IT', $status->getCountryCodes());
        foreach ($countryCodes as $countryCode) {
            $countryStatus = $status->getCountryStatus($countryCode);
            $this->assertInstanceOf(Vies\CheckStatus\Response\CountryStatus::class, $countryStatus);
            $this->assertSame($countryCode, $countryStatus->getCountryCode());
            $availability = $countryStatus->getAvailability();
            $this->assertContains($availability, [
                Vies\CheckStatus\Response\CountryStatus::AVAILABILITY_AVAILABLE,
                Vies\CheckStatus\Response\CountryStatus::AVAILABILITY_MONITORING_DISABLED,
                Vies\CheckStatus\Response\CountryStatus::AVAILABILITY_UNAVAILABLE,
            ]);
            if ($availability === Vies\CheckStatus\Response\CountryStatus::AVAILABILITY_AVAILABLE) {
                $this->assertTrue($countryStatus->isAvailable());
            } else {
                $this->assertFalse($countryStatus->isAvailable());
            }
        }
    }

    private function checkGoodVatNumber(ViesClientWrapper $vies)
    {
        $request = new Vies\CheckVat\Request();
        $request->setCountryCode('IT')->setVatNumber('00159560366');
        $now = new DateTimeImmutable('now');
        $beforeRequest = $now->sub(new DateInterval('PT1S'));
        $response = $vies->checkVatNumber($request);
        $now = new DateTimeImmutable('now');
        $afterRequest = $now->add(new DateInterval('PT1S'));
        $this->assertInstanceOf(Vies\CheckVat\Response::class, $response);
        $this->assertSame('IT', $response->getCountryCode());
        $this->assertSame('00159560366', $response->getVatNumber());
        $requestDate = $response->getRequestDate();
        $this->assertInstanceOf(DateTimeImmutable::class, $requestDate);
        $this->assertSame($beforeRequest->getTimezone()->getName(), $requestDate->getTimezone()->getName());
        $this->assertGreaterThanOrEqual($beforeRequest, $requestDate);
        $this->assertLessThanOrEqual($afterRequest, $requestDate);
        $this->assertSame(true, $response->isValid());
        $this->assertIsString($response->getRequestIdentifier());
        $this->assertIsString($response->getName());
        $this->assertMatchRegExp('/\bFerrari\b/i', $response->getName());
        $this->assertIsString($response->getAddress());
        $this->assertMatchRegExp('/\bModena\b/i', $response->getAddress());
        $this->assertIsString($response->getTraderName());
        $this->assertIsString($response->getTraderStreet());
        $this->assertIsString($response->getTraderPostalCode());
        $this->assertIsString($response->getTraderCity());
        $this->assertIsString($response->getTraderCompanyType());
        $matches = [
            Vies\CheckVat\Response::MATCH_VALID,
            Vies\CheckVat\Response::MATCH_INVALID,
            Vies\CheckVat\Response::MATCH_NOT_PROCESSED,
        ];
        $this->assertContains($response->getTraderNameMatch(), $matches);
        $this->assertContains($response->getTraderStreetMatch(), $matches);
        $this->assertContains($response->getTraderPostalCodeMatch(), $matches);
        $this->assertContains($response->getTraderCityMatch(), $matches);
        $this->assertContains($response->getTraderCompanyTypeMatch(), $matches);
    }

    private function checkWrongVatNumber(ViesClientWrapper $vies)
    {
        $request = new Vies\CheckVat\Request();
        $request->setCountryCode('IT')->setVatNumber('0015956036');
        $now = new DateTimeImmutable('now');
        $beforeRequest = $now->sub(new DateInterval('PT1S'));
        $response = $vies->checkVatNumber($request);
        $now = new DateTimeImmutable('now');
        $afterRequest = $now->add(new DateInterval('PT1S'));
        $this->assertInstanceOf(Vies\CheckVat\Response::class, $response);
        $this->assertSame('IT', $response->getCountryCode());
        $this->assertSame('0015956036', $response->getVatNumber());
        $requestDate = $response->getRequestDate();
        $this->assertInstanceOf(DateTimeImmutable::class, $requestDate);
        $this->assertSame($beforeRequest->getTimezone()->getName(), $requestDate->getTimezone()->getName());
        $this->assertGreaterThanOrEqual($beforeRequest, $requestDate);
        $this->assertLessThanOrEqual($afterRequest, $requestDate);
        $this->assertSame(false, $response->isValid());
        $this->assertIsString($response->getRequestIdentifier());
        $this->assertIsString($response->getName());
        $this->assertMatchRegExp('/^\W*/i', $response->getName());
        $this->assertIsString($response->getAddress());
        $this->assertMatchRegExp('/^\W*$/i', $response->getAddress());
        $this->assertIsString($response->getTraderName());
        $this->assertIsString($response->getTraderStreet());
        $this->assertIsString($response->getTraderPostalCode());
        $this->assertIsString($response->getTraderCity());
        $this->assertIsString($response->getTraderCompanyType());
        $matches = [
            Vies\CheckVat\Response::MATCH_VALID,
            Vies\CheckVat\Response::MATCH_INVALID,
            Vies\CheckVat\Response::MATCH_NOT_PROCESSED,
        ];
        $this->assertContains($response->getTraderNameMatch(), $matches);
        $this->assertContains($response->getTraderStreetMatch(), $matches);
        $this->assertContains($response->getTraderPostalCodeMatch(), $matches);
        $this->assertContains($response->getTraderCityMatch(), $matches);
        $this->assertContains($response->getTraderCompanyTypeMatch(), $matches);
    }

    private function checkWrongCountryCode(ViesClientWrapper $vies)
    {
        $request = new Vies\CheckVat\Request('UA', '00159560366');
        $exception = null;
        try {
            $vies->checkVatNumber($request);
        } catch (IOError\Vies $x) {
            $exception = $x;
        }
        $this->assertNotNull($exception);
        $this->assertSame('INVALID_INPUT', $exception->getViesCode());
        $this->assertStringContainsString('[INVALID_INPUT]', $exception->getMessage());
    }
}
