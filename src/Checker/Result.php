<?php

namespace VATLib\Checker;

use VATLib\Exception;
use VATLib\Format;
use VATLib\Vies\CheckVat\Response;

class Result
{
    /**
     * @var \VATLib\Format|null
     */
    private $format = null;

    /**
     * @var string
     */
    private $shortVatNumber = '';

    /**
     * @var bool|null
     */
    private $syntaxValid = null;

    /**
     * @var \VATLib\Vies\CheckVat\Response|null
     */
    private $viesResult = null;

    /**
     * @var \VATLib\Exception
     */
    private $exceptions = [];

    /**
     * @var bool|null
     */
    private $overrideIsLongFormPreferred = null;

    /**
     * @param string $shortVatNumber
     * @param \VATLib\Format|null $format
     *
     * @return static
     */
    public static function create($shortVatNumber, $format = null)
    {
        return new static($shortVatNumber, $format);
    }

    /**
     * @param string $shortVatNumber
     * @param \VATLib\Format|null $format
     */
    public function __construct($shortVatNumber, $format = null)
    {
        $this->shortVatNumber = (string) $shortVatNumber;
        $this->format = $format instanceof Format ? $format : null;
    }

    /**
     * Get the format definition for the VAT number (if available).
     *
     * @return \VATLib\Format|null
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Is the VAT number valid?
     *
     * @return bool|null Returns NULL if we don't know if it's valid
     */
    public function isValid()
    {
        $syntaxIsValid = $this->isSyntaxValid();
        if ($syntaxIsValid !== true) {
            return $syntaxIsValid;
        }
        $viesResult = $this->getViesResult();
        if ($viesResult !== null) {
            if ($viesResult->isValid() === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Is the VAT number invalid?
     *
     * @return bool|null Returns NULL if we don't know if it's invalid
     */
    public function isInvalid()
    {
        $isValid = $this->isValid();

        return $isValid === null ? null : !$isValid;
    }

    /**
     * Get the "short" form of the VAT number
     *
     * @return string
     *
     * @example 12345678901 for Italian VAT numbers
     * @example CHE123456789 for Swiss VAT numbers
     */
    public function getShortVatNumber()
    {
        return $this->shortVatNumber;
    }

    /**
     * Get the "long" form of the VAT number
     *
     * @return string
     *
     * @example IT12345678901 for Italian VAT numbers
     * @example CHE-123.456.789 for Swiss VAT numbers
     */
    public function getLongVatNumber()
    {
        $format = $this->getFormat();

        return $format === null ? $this->getShortVatNumber() : $format->convertShortToLongForm($this->getShortVatNumber());
    }

    /**
     * Is the VAT number syntactically correct?
     *
     * @return bool|null Return NULL if we don't know if the syntax is valid
     */
    public function isSyntaxValid()
    {
        return $this->syntaxValid;
    }

    /**
     * Is the VAT number syntactically correct?
     *
     * @param bool|null $value Use NULL if we don't know if the syntax is valid
     *
     * @return $this
     */
    public function setSyntaxValid($value)
    {
        $this->syntaxValid = $value === null ? null : (bool) $value;

        return $this;
    }

    /**
     * Get the VIES check response (available for VIES formats of VAT numbers syntactically valid)
     *
     * @return \VATLib\Vies\CheckVat\Response|null
     */
    public function getViesResult()
    {
        return $this->viesResult;
    }

    /**
     * Set the VIES check response.
     *
     * @param \VATLib\Vies\CheckVat\Response|null $value
     *
     * @return $this
     */
    public function setViesResult($value = null)
    {
        $this->viesResult = $value instanceof Response ? $value : null;

        return $this;
    }

    /**
     * Register an exception thrown during the validation of the VAT number.
     *
     * @return $this
     */
    public function addException(Exception $value)
    {
        $this->exceptions[] = $value;

        return $this;
    }

    /**
     * Exceptions were thrown during the validation of the VAT number?
     *
     * @return bool
     */
    public function hasExceptions()
    {
        return $this->exceptions !== [];
    }

    /**
     * Get the exceptions thrown during the validation of the VAT number.
     *
     * @return \VATLib\Exception[]
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }

    /**
     * Force the "long" form of the VAT number.
     *
     * @param bool|null $value set to NULL to use the configuration of the format associated to the VAT number
     *
     * @return $this
     */
    public function overrideIsLongFormPreferred($value)
    {
        $this->overrideIsLongFormPreferred = $value === null ? null : (bool) $value;

        return $this;
    }

    /**
     * #[\ReturnTypeWillChange]
     */
    public function __toString()
    {
        $longForm = $this->overrideIsLongFormPreferred;
        if ($longForm === null) {
            $format = $this->getFormat();
            $longForm = $format === null ? false : $format->isLongFormPreferred();
        }

        return $longForm ? $this->getLongVatNumber() : $this->getShortVatNumber();
    }
}
