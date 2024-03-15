<?php

namespace VATLib\Service;

trait Iso7064
{
    /**
     * Calculate an ISO/IEC 7064:2003 MOD 11-10 checksum
     *
     * @param string $numeric
     * @param int $length
     *
     * @return int
     */
    protected function mod11_10($numeric, $length)
    {
        $carry = 10;
        for ($index = 0; $index < $length; $index++) {
            $carry = ($carry + (int) $numeric[$index]) % 10;
            if ($carry === 0) {
                $carry = 9;
            } else {
                $carry = ($carry << 1) % 11;
            }
        }

        return $carry === 1 ? 0 : (11 - $carry);
    }
}
