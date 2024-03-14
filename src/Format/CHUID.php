<?php

namespace VATLib\Format;

use VATLib\Format;

/**
 * https://www.bfs.admin.ch/bfs/en/home/registers/enterprise-register/enterprise-identification/uid-general.html
 */
class CHUID implements Format
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
        return 'CH';
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
        $vatNumber = is_string($vatNumber) ? strtoupper(preg_replace('/[\-.\s]+/', '', $vatNumber)) : '';
        if ($vatNumber === '' || preg_match('/^(' . static::RX . ')$/D', $vatNumber) !== 1) {
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
        $chunks = str_split($shortVatNumber, 3);

        return implode('.', $chunks);
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
