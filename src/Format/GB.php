<?php

namespace VATLib\Format;

use VATLib\Format;

/**
 * https://www.bmf.gv.at/dam/jcr:6c874c4a-9f30-49d3-8da8-b7405f5aa944/BMF_UID_Konstruktionsregeln_Stand_November%202020.pdf
 */
class GB implements Format
{
    /**
     * @var string
     * @private
     */
    const RX1 = 'GD[0-4][0-9]{2}';

    /**
     * @var string
     * @private
     */
    const RX2 = 'HA[5-9][0-9]{2}';

    /**
     * @var string
     * @private
     */
    const RX3 = '[0-9]{9}';

    /**
     * @var string
     * @private
     */
    const RX4 = '[0-9]{12}';

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::getCountryCode()
     */
    public function getCountryCode()
    {
        return 'GB';
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::getVatNumberPrefix()
     */
    public function getVatNumberPrefix()
    {
        return 'GB';
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::formatShort()
     */
    public function formatShort($vatNumber)
    {
        return $this->doFormatShortWithPrefix($vatNumber, $this->getVatNumberPrefix());
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
     * @param string $vatNumber
     *
     * @return string
     */
    protected function doFormatShortWithPrefix($vatNumber, $prefix)
    {
        $vatNumber = is_string($vatNumber) ? strtoupper(preg_replace('/^' . $prefix . '|[\-.\s]/i', '', $vatNumber)) : '';
        if ($vatNumber === '') {
            return '';
        }
        if (preg_match('/^(' . static::RX1 . ')$/D', $vatNumber) === 1 || preg_match('/^(' . static::RX2 . ')$/D', $vatNumber) === 1) {
            return $vatNumber;
        }
        if (preg_match('/^(' . static::RX3 . ')$/D', $vatNumber) === 1 || preg_match('/^(' . static::RX4 . ')$/D', $vatNumber) === 1) {
            return $this->checkControlCode($vatNumber) ? $vatNumber : '';
        }

        return '';
    }

    /**
     * @param string $vatNumber
     *
     * @return bool
     */
    private function checkControlCode($vatNumber)
    {
        $multipliers = [8, 7, 6, 5, 4, 3, 2];
        $sum = (int) substr($vatNumber, 7, 2);
        for ($index = 0; $index <= 6; $index++) {
            $sum += $multipliers[$index] * (int) $vatNumber[$index];
        }

        return ($sum % 97) === 0 || (($sum + 55) % 97) === 0;
    }
}
