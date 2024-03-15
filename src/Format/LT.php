<?php

namespace VATLib\Format;

/**
 * @see https://www.bmf.gv.at/dam/jcr:6c874c4a-9f30-49d3-8da8-b7405f5aa944/BMF_UID_Konstruktionsregeln_Stand_November%202020.pdf
 */
class LT implements Vies
{
    /**
     * @var string
     * @private
     */
    const RX1 = '[0-9]{9}';

    /**
     * @var string
     * @private
     */
    const RX2 = '[0-9]{10}1[0-9]';

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::getCountryCode()
     */
    public function getCountryCode()
    {
        return 'LT';
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
        return 'LT';
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::formatShort()
     */
    public function formatShort($vatNumber)
    {
        $vatNumber = is_string($vatNumber) ? preg_replace('/^LT|[\-.\s]/i', '', $vatNumber) : '';
        if ($vatNumber === '') {
            return '';
        }
        if (preg_match('/^(' . static::RX1 . ')$/D', $vatNumber) === 1) {
            return $this->checkControlCode1($vatNumber) ? $vatNumber : '';
        }
        if (preg_match('/^(' . static::RX2 . ')$/D', $vatNumber) === 1) {
            return $this->checkControlCode2($vatNumber) ? $vatNumber : '';
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
        $multipliers = [1, 2, 3, 4, 5, 6, 7, 8];
        $sum = 0;
        for ($index = 0; $index <= 7; $index++) {
            $sum += $multipliers[$index] * (int) $vatNumber[$index];
        }
        $controlCode = $sum % 11;
        if ($controlCode === 10) {
            $multipliers = [3, 4, 5, 6, 7, 8, 9, 1];
            $sum = 0;
            for ($index = 0; $index <= 7; $index++) {
                $sum += $multipliers[$index] * (int) $vatNumber[$index];
            }
            $controlCode = $sum % 11;
            if ($controlCode === 10) {
                $controlCode = 0;
            }
        }

        return $vatNumber[8] === (string) $controlCode;
    }

    /**
     * @param string $vatNumber
     *
     * @return bool
     */
    private function checkControlCode2($vatNumber)
    {
        $multipliers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 1, 2];
        $sum = 0;
        for ($index = 0; $index <= 10; $index++) {
            $sum += $multipliers[$index] * (int) $vatNumber[$index];
        }
        $controlCode = $sum % 11;
        if ($controlCode === 10) {
            $multipliers = [3, 4, 5, 6, 7, 8, 9, 1, 2, 3, 4];
            $sum = 0;
            for ($index = 0; $index <= 10; $index++) {
                $sum += $multipliers[$index] * (int) $vatNumber[$index];
            }
            $controlCode = $sum % 11;
            if ($controlCode === 10) {
                $controlCode = 0;
            }
        }

        return $vatNumber[11] === (string) $controlCode;
    }
}
