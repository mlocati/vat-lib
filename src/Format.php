<?php

namespace VATLib;

interface Format
{
    const REGION_EUROPEAN_UNION = 'EU';

    /**
     * Get the ISO-3166 Alpha-2 Country code (if applicable).
     *
     * @return string empty string for non country-specific formats.
     *
     * @see https://www.iso.org/obp/ui/#search/code/
     */
    public function getCountryCode();

    /**
     * Get the fiscal region (if applicable).
     *
     * @return string empty string if not applicable/available.
     *
     * @see \VATLib\Format::REGION_EUROPEAN_UNION
     */
    public function getFiscalRegion();

    /**
     * Get the (optional) prefix of a VAT number expressed in the "long" form.
     *
     * @return string empty string if none
     */
    public function getVatNumberPrefix();

    /**
     * Check and normalize a VAT number.
     *
     * @param string|mixed $vatNumber the VAT number to be checked/normalized
     *
     * @throws \VATLib\Exception\MissingPHPExtensions in case of missing required PHP extensions
     * @throws \VATLib\Exception in case of other errors
     *
     * @return string the normalized VAT number (empty string if the syntax of the VAT number is not correct)
     */
    public function formatShort($vatNumber);

    /**
     * Format a VAT number to the "long" version.
     *
     * @param string $shortVatNumber the VAT number to be formatted (as returned by formatShort())
     *
     * @return string
     */
    public function convertShortToLongForm($shortVatNumber);

    /**
     * Should the "long" format be preferred?
     *
     * @return bool
     */
    public function isLongFormPreferred();
}
