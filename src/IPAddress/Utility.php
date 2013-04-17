<?php
/**
 * Copyright (c) 2013 Gijs Kunze
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IPAddress;

use IPAddress\Exceptions\InvalidFormatException;

/**
 * Class Utility
 *
 * Utility functions for parsing IP addresses
 *
 * @package IPAddress
 */
class Utility {

    /**
     * Parses the given variable as an unsigned integer. This will actually return a signed integer since php doesn't
     * support unsigned ints, however with some trickery (like an intermediate casting to float) it should work.
     *
     * @param $number
     * @param int $bits
     * @return int
     * @throws Exceptions\InvalidFormatException
     */
    public static function parseUint($number, $bits = 8)
    {
        if($bits < 0) {
            throw new InvalidFormatException("Negative amount bits aren't possible");
        }

        if($bits > (PHP_INT_SIZE * 8)) {
            throw new InvalidFormatException("Can handle bit sizes greater than $bits on a " . (PHP_INT_SIZE * 8) . "-bit platform");
        }

        $mask = ~(int)(pow(2, $bits) - 1);

        if(is_string($number)) {
            // Match octal string
            if(preg_match("/^0([0-7]+)$/", $number, $m)) {
                $number = (int)octdec($m[1]);
            }
            // Match decimal strings
            else if(preg_match("/^(0|[1-9][0-9]*)$/", $number, $m)) {
                // Handle integers larger than 0x7fffffff in php32 bit
                if(((int)$m[1]) != $m[1]) {
                    $m[1] = (int)(float)$m[1];
                }
                $number = (int)$m[1];
            }
            // Match hexadecimal strings
            else if(preg_match("/^0x([0-9a-f]+)$/i", $number, $m)) {
                $number = (int)hexdec($m[1]);
            } else {
                throw new InvalidFormatException("Invalid number format $number");
            }
        }

        if(is_integer($number)) {
            if($number & $mask) {
                throw new InvalidFormatException("$number out of bounds");
            }
        } else {
            throw new InvalidFormatException("Can parse " . gettype($number) . " as unsigned integer");
        }

        return $number;
    }
}
