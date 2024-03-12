<?php

namespace VATLib\Vies\CheckVat;

use DateTimeImmutable;

class Response
{
    const MATCH_VALID = 'VALID';

    const MATCH_INVALID = 'INVALID';

    const MATCH_NOT_PROCESSED = 'NOT_PROCESSED';

    /**
     * @var array
     */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->extractString('countryCode');
    }

    /**
     * @return string
     */
    public function getVatNumber()
    {
        return $this->extractString('vatNumber');
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getRequestDate()
    {
        return $this->extractDateTime('requestDate');
    }

    /**
     * @return bool|null
     */
    public function isValid()
    {
        return $this->extractBool('valid');
    }

    /**
     * @return string
     */
    public function getRequestIdentifier()
    {
        return $this->extractString('requestIdentifier');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->extractString('name');
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->extractString('address');
    }

    /**
     * @return string
     */
    public function getTraderName()
    {
        return $this->extractString('traderName');
    }

    /**
     * @return string
     */
    public function getTraderStreet()
    {
        return $this->extractString('traderStreet');
    }

    /**
     * @return string
     */
    public function getTraderPostalCode()
    {
        return $this->extractString('traderPostalCode');
    }

    /**
     * @return string
     */
    public function getTraderCity()
    {
        return $this->extractString('traderCity');
    }

    /**
     * @return string
     */
    public function getTraderCompanyType()
    {
        return $this->extractString('traderCompanyType');
    }

    /**
     * @return string
     *
     * @see \VATLib\Vies\CheckVat\Response::MATCH_VALID
     * @see \VATLib\Vies\CheckVat\Response::MATCH_INVALID
     * @see \VATLib\Vies\CheckVat\Response::MATCH_NOT_PROCESSED
     */
    public function getTraderNameMatch()
    {
        return $this->extractString('traderNameMatch');
    }

    /**
     * @return string
     *
     * @see \VATLib\Vies\CheckVat\Response::MATCH_VALID
     * @see \VATLib\Vies\CheckVat\Response::MATCH_INVALID
     * @see \VATLib\Vies\CheckVat\Response::MATCH_NOT_PROCESSED
     */
    public function getTraderStreetMatch()
    {
        return $this->extractString('traderStreetMatch');
    }

    /**
     * @return string
     *
     * @see \VATLib\Vies\CheckVat\Response::MATCH_VALID
     * @see \VATLib\Vies\CheckVat\Response::MATCH_INVALID
     * @see \VATLib\Vies\CheckVat\Response::MATCH_NOT_PROCESSED
     */
    public function getTraderPostalCodeMatch()
    {
        return $this->extractString('traderPostalCodeMatch');
    }

    /**
     * @return string
     *
     * @see \VATLib\Vies\CheckVat\Response::MATCH_VALID
     * @see \VATLib\Vies\CheckVat\Response::MATCH_INVALID
     * @see \VATLib\Vies\CheckVat\Response::MATCH_NOT_PROCESSED
     */
    public function getTraderCityMatch()
    {
        return $this->extractString('traderCityMatch');
    }

    /**
     * @return string
     *
     * @see \VATLib\Vies\CheckVat\Response::MATCH_VALID
     * @see \VATLib\Vies\CheckVat\Response::MATCH_INVALID
     * @see \VATLib\Vies\CheckVat\Response::MATCH_NOT_PROCESSED
     */
    public function getTraderCompanyTypeMatch()
    {
        return $this->extractString('traderCompanyTypeMatch');
    }

    /**
     * @return array
     */
    public function getRawData()
    {
        return $this->data;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function extractString($key)
    {
        return isset($this->data[$key]) && is_string($this->data[$key]) ? $this->data[$key] : '';
    }

    /**
     * @param string $key
     *
     * @return bool|null
     */
    private function extractBool($key)
    {
        return isset($this->data[$key]) && is_bool($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * @param string $key
     *
     * @return \DateTimeImmutable|null
     */
    private function extractDateTime($key)
    {
        $value = $this->extractString($key);
        if ($value === '') {
            return null;
        }
        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return null;
        }
        $result = new DateTimeImmutable();

        return $result->setTimestamp($timestamp);
    }
}
