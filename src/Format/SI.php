<?php

namespace VATLib\Format;

/**
 * https://www.bmf.gv.at/dam/jcr:6c874c4a-9f30-49d3-8da8-b7405f5aa944/BMF_UID_Konstruktionsregeln_Stand_November%202020.pdf
 */
class SI implements Vies
{
    /**
     * @var string
     * @private
     */
    const RX = '[1-9][0-9]{7}';

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::getCountryCode()
     */
    public function getCountryCode()
    {
        return 'SI';
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
        return 'SI';
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::formatShort()
     */
    public function formatShort($vatNumber)
    {
        $vatNumber = is_string($vatNumber) ? preg_replace('/^SI|[\-.\s]/i', '', $vatNumber) : '';
        if ($vatNumber === '' || preg_match('/^(' . static::RX . ')$/D', $vatNumber) !== 1) {
            return '';
        }
        if (!$this->checkControlCode($vatNumber)) {
            return '';
        }

        return $vatNumber;
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
    private function checkControlCode($vatNumber)
    {
        $multipliers = [8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($index = 0; $index <= 6; $index++) {
            $sum += $multipliers[$index] * (int) $vatNumber[$index];
        }
        $controlCode = 11 - ($sum % 11);
        switch ($controlCode) {
            case 10:
                $controlCode = 0;
                break;
            case 11:
                return false;
        }

        return $vatNumber[7] === (string) $controlCode;
    }
}
