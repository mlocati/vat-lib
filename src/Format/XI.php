<?php

namespace VATLib\Format;

class XI extends GB
{
    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::getVatNumberPrefix()
     * @see \VATLib\Format\GB::getVatNumberPrefix()
     */
    public function getVatNumberPrefix()
    {
        return 'XI';
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::isLongFormPreferred()
     * @see \VATLib\Format\GB::isLongFormPreferred()
     */
    public function isLongFormPreferred()
    {
        return true;
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
}
