<?php
/**
 * Copyright (c) 2013 Gijs Kunze
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IPAddress;


use IPAddress\Exceptions\InvalidFormatException;

abstract class IPAddress implements \ArrayAccess
{
    const IPv4 = 4;
    const IPv6 = 6;

    /**
     * Get the version of the IP address
     *
     * @return int
     */
    public abstract function version();

    public abstract function __toString();


    public static function fromString($string) {
        $string = trim($string);

        try {
            return IPv4\Range::fromString($string);
        } catch(InvalidFormatException $e) {}

        try {
            return IPv4\Subnet::fromString($string);
        } catch(InvalidFormatException $e) {}

        try {
            return new IPv4\Address($string);
        } catch(InvalidFormatException $e) {}

        throw new InvalidFormatException("Unknown address format");
    }
}
