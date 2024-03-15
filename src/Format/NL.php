<?php

namespace VATLib\Format;

use VATLib\Exception\MissingPHPExtensions;
use VATLib\Service\Modulo;

/**
 * https://www.bmf.gv.at/dam/jcr:6c874c4a-9f30-49d3-8da8-b7405f5aa944/BMF_UID_Konstruktionsregeln_Stand_November%202020.pdf
 */
class NL implements Vies
{
    use Modulo;

    /**
     * @var string
     * @private
     */
    const RX1 = '[0-9]{9}B[0-9]{2}';

    /**
     * @var string
     * @private
     */
    const RX2 = '[A-Z0-9+*]{12}';

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::getCountryCode()
     */
    public function getCountryCode()
    {
        return 'NL';
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::getFiscalRegion()
     */
    public function getFiscalRegion()
    {
        return static::REGION_EUROPEAN_UNION;
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::getVatNumberPrefix()
     */
    public function getVatNumberPrefix()
    {
        return 'NL';
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::formatShort()
     */
    public function formatShort($vatNumber)
    {
        $vatNumber = is_string($vatNumber) ? strtoupper(preg_replace('/^NL|[\-.\s]/i', '', $vatNumber)) : '';
        if ($vatNumber === '') {
            return '';
        }
        if (preg_match('/^(' . static::RX1 . ')$/D', $vatNumber) === 1 && $this->checkControlCode1($vatNumber)) {
            return $vatNumber;
        }
        if (preg_match('/^(' . static::RX2 . ')$/D', $vatNumber) === 1 && $this->checkControlCode2($vatNumber)) {
            return $vatNumber;
        }

        return '';
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::convertShortToLongForm()
     */
    public function convertShortToLongForm($shortVatNumber)
    {
        return $this->getVatNumberPrefix() . $shortVatNumber;
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::isLongFormPreferred()
     */
    public function isLongFormPreferred()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format\Vies::getViesCountryCode()
     */
    public function getViesCountryCode()
    {
        return $this->getVatNumberPrefix();
    }

    /**
     * @param string $vatNumber
     *
     * @return bool
     */
    private function checkControlCode1($vatNumber)
    {
        $multipliers = [9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($index = 0; $index <= 7; $index++) {
            $sum += $multipliers[$index] * (int) $vatNumber[$index];
        }
        $controlCode = $sum % 11;
        if ($controlCode === 10) {
            return false;
        }

        return $vatNumber[8] === (string) $controlCode;
    }

    /**
     * @param string $vatNumber
     *
     * @throws \VATLib\Exception\MissingPHPExtensions
     *
     * @return bool
     */
    private function checkControlCode2($vatNumber)
    {
        $numeric = '';
        $prefixedVatNumber = 'NL' . $vatNumber;
        $azBase = 10 - ord('A');
        for ($index = 0; $index <= 13; $index++) {
            $char = $prefixedVatNumber[$index];
            if ($char >= '0' && $char <= '9') {
                $numeric .= $char;
            } elseif ($char === '+') {
                $numeric .= '36';
            } elseif ($char === '*') {
                $numeric .= '37';
            } else {
                $numeric .= (string) (ord($char) + $azBase);
            }
        }
        $checkControl = $this->getModulo97($numeric);

        return $checkControl === 1;
    }

    /**
     * @param string $numeric
     *
     * @throws \VATLib\Exception\MissingPHPExtensions
     *
     * @return int
     */
    private function getModulo97($numeric)
    {
        $result = $this->getModulo($numeric, 97);
        if ($result === null) {
            throw new MissingPHPExtensions(['bcmath', 'gmp'], 'Please install the BCMath or the GMP PHP extension to formatShort the Dutch VAT numbers on 32-bit systems');
        }
        return $result;
    }
}
