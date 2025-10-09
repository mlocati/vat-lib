<?php

namespace VATLib\Format;

use VATLib\Format;

/**
 * @see https://en.wikipedia.org/wiki/VAT_identification_number
 */
class BR implements Format
{
    /**
     * @var string
     * @private
     */
    const RX = '^([0-9]{2}\.[0-9]{3}\.[0-9]{3}\/[0-9]{4}-[0-9]{2})|([0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2})$';

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::getCountryCode()
     */
    public function getCountryCode()
    {
        return 'BR';
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::getFiscalRegion()
     */
    public function getFiscalRegion()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::getVatNumberPrefix()
     */
    public function getVatNumberPrefix()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::formatShort()
     */
    public function formatShort($vatNumber)
    {
        if ($vatNumber === '' || preg_match('/^(' . static::RX . ')$/D', $vatNumber) !== 1) {
            return '';
        }

        return str_replace(['.', '/', '-'], '', $vatNumber);
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::convertShortToLongForm()
     */
    public function convertShortToLongForm($shortVatNumber)
    {
        if (strlen($shortVatNumber) === 14) {
            return substr($shortVatNumber, 0, 2) . '.' . substr($shortVatNumber, 2, 3) . '.' . substr($shortVatNumber, 5, 3) . '/' . substr($shortVatNumber, 8, 4) . '-' . substr($shortVatNumber, 12, 2);
        }

        return substr($shortVatNumber, 0, 3) . '.' . substr($shortVatNumber, 3, 3) . '.' . substr($shortVatNumber, 6, 3) . '-' . substr($shortVatNumber, 9, 2);
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::isLongFormPreferred()
     */
    public function isLongFormPreferred()
    {
        return true;
    }
}
