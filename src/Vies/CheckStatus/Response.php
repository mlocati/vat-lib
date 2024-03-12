<?php

namespace VATLib\Vies\CheckStatus;

class Response
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var \VATLib\Vies\CheckStatus\Response\VowStatus|null
     */
    private $vowStatus;

    /**
     * @var array|null
     */
    private $countryCodesMap;

    /**
     * @var \VATLib\Vies\CheckStatus\Response\CountryStatus[]
     */
    private $countryStatuses = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return \VATLib\Vies\CheckStatus\Response\VowStatus
     */
    public function getVowStatus()
    {
        if ($this->vowStatus === null) {
            $this->vowStatus = new Response\VowStatus(isset($this->data['vow']) && is_array(($this->data['vow'])) ? $this->data['vow'] : []);
        }

        return $this->vowStatus;
    }

    /**
     * @return string[]
     */
    public function getCountryCodes()
    {
        return array_keys($this->getCountryCodesMap());
    }

    /**
     * @param string $countryCode
     *
     * @return \VATLib\Vies\CheckStatus\Response\CountryStatus|null
     */
    public function getCountryStatus($countryCode)
    {
        if (!is_string($countryCode)) {
            return null;
        }
        if (!isset($this->countryStatuses[$countryCode])) {
            $map = $this->getCountryCodesMap();
            if (!isset($map[$countryCode])) {
                return null;
            }
            $this->countryStatuses[$countryCode] = new Response\CountryStatus($this->data['countries'][$map[$countryCode]]);

        }

        return $this->countryStatuses[$countryCode];
    }

    /**
     * @return array
     */
    public function getRawData()
    {
        return $this->data;
    }

    /**
     * return array
     */
    private function getCountryCodesMap()
    {
        if ($this->countryCodesMap === null) {
            $this->countryCodesMap = $this->extractCountryCodesMap();
        }

        return $this->countryCodesMap;
    }

    /**
     * @return string[]
     */
    private function extractCountryCodesMap()
    {
        if (!isset($this->data['countries']) || !is_array($this->data['countries'])) {
            return [];
        }
        $countryCodesMap = [];
        foreach ($this->data['countries'] as $index => $country) {
            if (is_array($country) && isset($country['countryCode']) && is_string($country['countryCode'])) {
                $countryCodesMap[$country['countryCode']] = $index;
            }
        }
        ksort($countryCodesMap, SORT_STRING);

        return $countryCodesMap;
    }
}
