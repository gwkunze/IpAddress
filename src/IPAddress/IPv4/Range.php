<?php
/**
 * Copyright (c) 2013 Gijs Kunze
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IPAddress\IPv4;

use IPAddress\Exceptions\InvalidFormatException;
use IPAddress\IPAddress;
use IPAddress\Matcher;

/**
 * Class Range represents a range of IP addresses
 *
 * @package IPAddress\IPv4
 */
class Range implements Matcher
{

    /**
     * @var Address
     */
    private $ip1;

    /**
     * @var Address
     */
    private $ip2;

    /**
     * Create an IP range from a couple of IP addresses
     *
     * @param Address|string|array $ip1
     * @param Address|string|array $ip2
     */
    public function __construct($ip1, $ip2)
    {
        $ip1 = new Address($ip1);
        $ip2 = new Address($ip2);
        if ($ip1 < $ip2) {
            $this->ip1 = $ip1;
            $this->ip2 = $ip2;
        } else {
            $this->ip1 = $ip2;
            $this->ip2 = $ip1;
        }
    }

    /**
     * Create an IP range from a string
     *
     * @param $string
     * @throws \IPAddress\Exceptions\InvalidFormatException
     * @return \IPAddress\IPv4\Range
     */
    public static function fromString($string)
    {
        if (preg_match("/^(\\d+\\.\\d+\\.\\d+\\.\\d+)\\s*(\\-\\s*)?(\\d+\\.\\d+\\.\\d+\\.\\d+)$/", $string, $m)) {
            return new self($m[1], $m[3]);
        }

        throw new InvalidFormatException("Unknown IP-range format");
    }

    /**
     * Get the lower part of the range
     *
     * @return Address
     */
    public function ip1()
    {
        return $this->ip1;
    }

    /**
     * Get the upper part of the range
     *
     * @return Address
     */
    public function ip2()
    {
        return $this->ip2;
    }

    /**
     * Get a string representation of an IP range
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf("%s-%s", $this->ip1, $this->ip2);
    }

    /**
     * Tests whether the given address matches
     *
     * @param $address
     * @return bool
     */
    public function match($address)
    {
        if(!($address instanceof Address)) {
            return false;
        }

        return ($address >= $this->ip1 && $address <= $this->ip2);
    }
}
