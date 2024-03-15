<?php

namespace VATLib;

use VATLib\Checker\Result;
use VATLib\Vies\CheckVat;

class Checker
{
    /**
     * @var \VATLib\Format\Factory|null
     */
    private $formatFactory = null;

    /**
     * @var \VATLib\Vies\Client|null
     */
    private $viesClient = null;

    /**
     * Check a VAT number that comes with the country code.
     *
     * @param string|mixed $vatNumber
     * @param string|mixed $countryCode ISO 3166 alpha-2 country code
     *
     * @return \VATLib\Checker\Result
     *
     * @example checkLongVatNumber('IT00159560366')
     */
    public function check($vatNumber, $countryCode = '')
    {
        if (!is_string($vatNumber)) {
            $vatNumber = '';
        }
        $countryCode = is_string($countryCode) ? strtoupper($countryCode) : '';
        if ($vatNumber === '') {
            return Result::create('')->setSyntaxValid(false);
        }
        if ($countryCode === '') {
            $formats = $this->getFormatFactory()->getFormatsByPrefix($vatNumber);
        } else {
            $formats = $this->getFormatFactory()->getFormatsForCountry($countryCode);
        }
        $result = null;
        foreach ($formats as $format) {
            $check = $this->checkWith($vatNumber, $format);
            if ($check === null) {
                continue;
            }
            if ($result !== null) {
                $result = null;
                break;
            }
            $result = $check;
        }
        if ($result === null) {
            return Result::create($vatNumber)->setSyntaxValid($formats === [] ? null : false);
        }
        if ($countryCode === '') {
            $result->overrideIsLongFormPreferred(true);
        }
        if ($result->isSyntaxValid() === true) {
            if ($result->getFormat() instanceof Format\Vies) {
                $this->checkVies($result);
            }
        }

        return $result;
    }

    /**
     * Get the formats compatible with a VAT number.
     *
     * @param string|mixed $vatNumber
     * @param bool $ignoreErrors
     *
     * @throws \VATLib\Exception if $ignoreErrors is false and errors occur while checking formats (for example missing PHP extensions on 32-bit systems)
     *
     * @return \VATLib\Format[]
     */
    public function getApplicableFormats($vatNumber, $ignoreErrors = false)
    {
        if (!is_string($vatNumber) || $vatNumber === '') {
            return [];
        }
        $result = [];
        foreach ($this->getFormatFactory()->getAllFormats() as $format) {
            if ($ignoreErrors) {
                try {
                    $normalized = $format->formatShort($vatNumber);
                } catch (Exception $_) {
                    continue;
                }
            } else {
                $normalized = $format->formatShort($vatNumber);
            }
            if ($normalized !== '') {
                $result[] = $format;
            }
        }

        return $result;
    }

    /**
     * @return \VATLib\Format\Factory
     */
    public function getFormatFactory()
    {
        if ($this->formatFactory === null) {
            $this->formatFactory = new Format\Factory();
        }

        return $this->formatFactory;
    }

    /**
     * @return $this
     */
    public function setFormatFactory(Format\Factory $value)
    {
        $this->formatFactory = $value;

        return $this;
    }

    /**
     * @return \VATLib\Vies\Client
     */
    public function getViesClient()
    {
        if ($this->viesClient === null) {
            $this->viesClient = new Vies\Client();
        }

        return $this->viesClient;
    }

    /**
     * @return $this
     */
    public function setViesClient(Vies\Client $value)
    {
        $this->viesClient = $value;

        return $this;
    }

    /**
     * @param string $vatNumber
     *
     * @return \VATLib\Checker\Result|null
     */
    private function checkWith($vatNumber, Format $format)
    {
        try {
            $shortVatNumber = $format->formatShort($vatNumber);
            $exception = null;
        } catch (Exception $exception) {
        }
        if ($exception !== null) {
            return Result::create($vatNumber, $format)->addException($exception);
        }
        if ($shortVatNumber === '') {
            return null;
        }

        return Result::create($shortVatNumber, $format)->setSyntaxValid(true);
    }

    /**
     * @return void
     */
    private function checkVies(Result $result)
    {
        $request = new CheckVat\Request();
        $request
            ->setCountryCode($result->getFormat()->getViesCountryCode())
            ->setVatNumber($result->getShortVatNumber())
        ;
        try {
            $result->setViesResult($this->getViesClient()->checkVatNumber($request));
        } catch (Exception $x) {
            $result->addException($x);
        }
    }
}
