<?php

namespace VATLib\Vies\CheckVat;

use JsonSerializable;

class Request implements JsonSerializable
{
    /**
     * The VIES country code.
     *
     * @var string
     * @example 'IT' for Italy
     * @example 'EL' for Greece
     */
    private $countryCode = '';

    /**
     * @var string
     */
    private $vatNumber = '';

    /**
     * @var string
     */
    private $requesterMemberStateCode = '';

    /**
     * @var string
     */
    private $requesterNumber = '';

    /**
     * @var string
     */
    private $traderName = '';

    /**
     * @var string
     */
    private $traderStreet = '';

    /**
     * @var string
     */
    private $traderPostalCode = '';

    /**
     * @var string
     */
    private $traderCity = '';

    /**
     * @var string
     */
    private $traderCompanyType = '';

    /**
     * @param string $countryCode
     * @param string $vatNumber
     */
    public function __construct($countryCode = '', $vatNumber = '')
    {
        $this->countryCode = is_string($countryCode) ? $countryCode : '';
        $this->vatNumber = is_string($vatNumber) ? $vatNumber : '';
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCountryCode($value)
    {
        $this->countryCode = (string) $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getVatNumber()
    {
        return $this->vatNumber;
    }

    /**
     * @param string $value
     *
     * @return $this
     *
     * @return $this
     */
    public function setVatNumber($value)
    {
        $this->vatNumber = (string) $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequesterMemberStateCode()
    {
        return $this->requesterMemberStateCode;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setRequesterMemberStateCode($value)
    {
        $this->requesterMemberStateCode = (string) $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequesterNumber()
    {
        return $this->requesterNumber;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setRequesterNumber($value)
    {
        $this->requesterNumber = (string) $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getTraderName()
    {
        return $this->traderName;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTraderName($value)
    {
        $this->traderName = (string) $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getTraderStreet()
    {
        return $this->traderStreet;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTraderStreet($value)
    {
        $this->traderStreet = (string) $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getTraderPostalCode()
    {
        return $this->traderPostalCode;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTraderPostalCode($value)
    {
        $this->traderPostalCode = (string) $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getTraderCity()
    {
        return $this->traderCity;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTraderCity($value)
    {
        $this->traderCity = (string) $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getTraderCompanyType()
    {
        return $this->traderCompanyType;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTraderCompanyType($value)
    {
        $this->traderCompanyType = (string) $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see \JsonSerializable::jsonSerialize()
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $result = [
            'countryCode' => $this->getCountryCode(),
            'vatNumber' => $this->getVatNumber(),
            'requesterMemberStateCode' => $this->getRequesterMemberStateCode(),
            'requesterNumber' => $this->getRequesterNumber(),
            'traderName' => $this->getTraderName(),
            'traderStreet' => $this->getTraderStreet(),
            'traderPostalCode' => $this->getTraderPostalCode(),
            'traderCity' => $this->getTraderCity(),
            'traderCompanyType' => $this->getTraderCompanyType(),
        ];

        return array_filter($result, static function ($value) {
            return $value !== '';
        });
    }
}
