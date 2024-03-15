<?php

namespace VATLib\Format;

/**
 * @see https://www.bmf.gv.at/dam/jcr:6c874c4a-9f30-49d3-8da8-b7405f5aa944/BMF_UID_Konstruktionsregeln_Stand_November%202020.pdf
 */
class IE implements Vies
{
    /**
     * Version 1 (old Style)
     *
     * @var string
     * @private
     */
    const RX1 = '[0-9][A-Z+*][0-9]{5}[A-W]';

    /**
     * Version 2 (new Style 8 characters).
     *
     * @var string
     * @private
     */
    const RX2 = '[0-9]{7}[A-W]';

    /**
     * Version 3 (new Style 9 characters).
     *
     * @var string
     * @private
     */
    const RX3 = '[0-9]{7}[A-W][A-I]';

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::getCountryCode()
     */
    public function getCountryCode()
    {
        return 'IE';
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
        return 'IE';
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::formatShort()
     */
    public function formatShort($vatNumber)
    {
        $vatNumber = is_string($vatNumber) ? strtoupper(preg_replace('/^IE|[\-.\s]/i', '', $vatNumber)) : '';
        if ($vatNumber === '') {
            return '';
        }
        if (preg_match('/^(' . static::RX1 . ')$/D', $vatNumber) === 1) {
            return $this->checkControlCode1($vatNumber) ? $vatNumber : '';
        }
        if (preg_match('/^(' . static::RX2 . ')$/D', $vatNumber) === 1) {
            return $this->checkControlCode2($vatNumber) ? $vatNumber : '';
        }
        if (preg_match('/^(' . static::RX3 . ')$/D', $vatNumber) === 1) {
            return $this->checkControlCode3($vatNumber) ? $vatNumber : '';
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
        $multipliers = [2, null, 7, 6, 5, 4, 3];
        $sum = 0;
        for ($index = 0; $index <= 6; $index++) {
            if ($multipliers[$index] !== null) {
                $sum += $multipliers[$index] * (int) $vatNumber[$index];
            }
        }
        $map = 'WABCDEFGHIJKLMNOPQRSTUV';

        return $vatNumber[7] === $map[$sum % 23];
    }

    /**
     * @param string $vatNumber
     *
     * @return bool
     */
    private function checkControlCode2($vatNumber)
    {
        $multipliers = [8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($index = 0; $index <= 6; $index++) {
            $sum += $multipliers[$index] * (int) $vatNumber[$index];
        }
        $map = 'WABCDEFGHIJKLMNOPQRSTUV';

        return $vatNumber[7] === $map[$sum % 23];
    }

    /**
     * @param string $vatNumber
     *
     * @return bool
     */
    private function checkControlCode3($vatNumber)
    {
        $letterToNumber = '_ABCDEFGHI';
        $multipliers = [8, 7, 6, 5, 4, 3, 2];
        $sum = 9 * strpos($letterToNumber, $vatNumber[8]);
        for ($index = 0; $index <= 6; $index++) {
            $sum += $multipliers[$index] * (int) $vatNumber[$index];
        }
        $map = 'WABCDEFGHIJKLMNOPQRSTUV';

        return $vatNumber[7] === $map[$sum % 23];
    }
}
