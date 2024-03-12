<?php

namespace VATLib\Format;

use VATLib\Format;

interface Vies extends Format
{
    /**
     * Get the country code to be used with the VIES online service.
     *
     * @return string
     */
    public function getViesCountryCode();
}
