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
 * Class Subnet represents an IP address with a netmask to specify a subnet
 *
 * @package IPAddress\IPv4
 */
class Subnet implements Matcher
{
    private $ip;

    /**
     * @var Address
     */
    private $netmask;

    /**
     * Construct an IPv4 Subnet from an IP address and a netmask
     *
     * @param Address|string|array $ip
     * @param Address|string|array $netmask
     */
    public function __construct($ip, $netmask)
    {
        $this->ip = new Address($ip);

        $this->netmask = new Address($netmask);
    }

    /**
     * Construct an IPv4 Subnet from a CIDR notation (#.#.#.#/#)
     *
     * @param Address|string|array $ip
     * @param int $cidr
     * @return Subnet
     */
    public static function fromCidr($ip, $cidr)
    {
        $netmask = (0xffffffff << (32 - $cidr)) & 0xffffffff;
        return new self($ip, $netmask);
    }

    /**
     * Construct an IPv4 Subnet from a string
     *
     * @param $string
     * @throws \IPAddress\Exceptions\InvalidFormatException
     * @return Subnet
     */
    public static function fromString($string)
    {
        $string = trim($string);

        if(preg_match("/^(\\d+.\\d+.\\d+.\\d+)\\s*((nm|netmask)\\s*)?(\\d+.\\d+.\\d+.\\d+)$/", $string, $m)) {
            return new self($m[1], $m[4]);
        }

        if(preg_match("~^(\\d+.\\d+.\\d+.\\d+)/(\\d+)$~", $string, $m)) {
            return self::fromCidr($m[1], (int)$m[2]);
        }

        throw new InvalidFormatException("Unknown Subnet format");
    }

    /**
     * Test whether the given IPv4 address is in the subnet
     *
     * @param Address $ip
     * @return bool
     */
    public function contains(Address $ip)
    {
        for ($i = 0; $i < 4; $i++) {
            if (($this->ip[$i] & $this->netmask[$i]) != ($ip[$i] & $this->netmask[$i])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Return whether this address is the network address (all-zeros)
     *
     * @return int
     */
    public function isNetworkAddress()
    {
        return ($this->ip->int() & (0xffffffff & ~$this->netmask->int())) === 0;
    }

    /**
     * Return whether this address is the networkś broadcast address (all-ones)
     *
     * @return int
     */
    public function isBroadCastAddress()
    {
        $netmask_inverted = (0xffffffff & ~$this->netmask->int());

        return ($this->ip->int() & $netmask_inverted) === $netmask_inverted;
    }

    /**
     * Get a string representation of the subnet
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf("%s netmask %s", $this->ip, $this->netmask);
    }

    /**
     * @param $ip
     * @return bool
     */
    public function match($ip) {
        if(!($ip instanceof Address)) {
            return false;
        }

        return $this->contains($ip);
    }
}
