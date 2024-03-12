<?php

namespace VATLib\Service;

trait Modulo
{
    /**
     * @param string $dividend a string containing a big integer
     * @param int $divisor
     *
     * @return int|null
     */
    protected function getModulo($dividend, $divisor)
    {
        $maxInt = (string) PHP_INT_MAX;
        $delta = strlen($maxInt) - strlen($dividend);
        if ($delta > 0 || ($delta === 0 && $dividend <= $maxInt)) {
            return ((int) $dividend) % $divisor;
        }
        if (extension_loaded('bcmath')) {
            return (int) bcmod($dividend, (string) $divisor);
        }
        if (extension_loaded('gmp')) {
            return (int) gmp_strval(gmp_mod($dividend, (string) $divisor));
        }

        return null;
    }
}
