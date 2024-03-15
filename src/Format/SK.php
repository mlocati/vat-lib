<?php

namespace VATLib\Format;

use VATLib\Exception\MissingPHPExtensions;
use VATLib\Service\Modulo;

/**
 * @see https://www.bmf.gv.at/dam/jcr:6c874c4a-9f30-49d3-8da8-b7405f5aa944/BMF_UID_Konstruktionsregeln_Stand_November%202020.pdf
 */
class SK implements Vies
{
    use Modulo;

    /**
     * @var string
     * @private
     */
    const RX = '[1-9][0-9][234789][0-9]{7}';

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::getCountryCode()
     */
    public function getCountryCode()
    {
        return 'SK';
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
        return 'SK';
    }

    /**
     * {@inheritdoc}
     *
     * @see \VATLib\Format::formatShort()
     */
    public function formatShort($vatNumber)
    {
        $vatNumber = is_string($vatNumber) ? preg_replace('/^SK|[\-.\s]/i', '', $vatNumber) : '';
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
     * @throws \VATLib\Exception\MissingPHPExtensions
     *
     * @return bool
     */
    private function checkControlCode($vatNumber)
    {
        $modulo = $this->getModulo11($vatNumber);

        return $modulo === 0;
    }

    /**
     * @param string $numeric
     *
     * @throws \VATLib\Exception\MissingPHPExtensions
     *
     * @return int
     */
    private function getModulo11($numeric)
    {
        $result = $this->getModulo($numeric, 11);
        if ($result === null) {
            throw new MissingPHPExtensions(['bcmath', 'gmp'], 'Please install the BCMath or the GMP PHP extension to formatShort the Slokav VAT numbers on 32-bit systems');
        }

        return $result;
    }
}
