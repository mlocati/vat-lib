<?php

namespace VATLib\Format;

/**
 * @see https://www.bmf.gv.at/dam/jcr:6c874c4a-9f30-49d3-8da8-b7405f5aa944/BMF_UID_Konstruktionsregeln_Stand_November%202020.pdf
 */
class EE implements Vies
{
    /**
     * @var string
     * @private
     */
    const RX = '[0-9]{9}';

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::getCountryCode()
     */
    public function getCountryCode()
    {
        return 'EE';
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
        return 'EE';
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::formatShort()
     */
    public function formatShort($vatNumber)
    {
        $vatNumber = is_string($vatNumber) ? preg_replace('/^EE|[\-.\s]/i', '', $vatNumber) : '';
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
        $multipliers = [3, 7, 1, 3, 7, 1, 3, 7];
        $sum = 0;
        for ($index = 0; $index <= 7; $index++) {
            $sum += $multipliers[$index] * (int) $vatNumber[$index];
        }
        $controlCode = (10 - $sum % 10) % 10;

        return $vatNumber[8] === (string) $controlCode;
    }
}
