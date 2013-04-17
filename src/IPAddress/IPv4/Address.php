<?php
/**
 * Copyright (c) 2013 Gijs Kunze
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IPAddress\IPv4;

use IPAddress\Exceptions\InvalidFormatException;
use IPAddress\Exceptions\UnknownClassException;
use IPAddress\IPAddress;
use IPAddress\Matcher;
use IPAddress\Utility;

/**
 * Class IPv4Address represents an IPv4 address
 *
 * @package IPAddress
 */
class Address extends IPAddress implements \ArrayAccess, Matcher
{
    const CLASS_A = "A";
    const CLASS_B = "B";
    const CLASS_C = "C";
    const CLASS_D = "D";
    const CLASS_E = "E";

    /**
     * Addresses are stored as an array of four short integers
     *
     * @var int[]
     */
    private $address = array(0, 0, 0, 0);

    /**
     * Create an instance from either a dotted quad (#.#.#.#) or an array
     *
     * @param $address
     * @throws \IPAddress\Exceptions\InvalidFormatException
     */
    public function __construct($address)
    {
        if($address instanceof Address) {
            $this->address = $address->address;
            return;
        }

        if (is_string($address)) {
            $address = explode(".", $address);

            if (count($address) == 1) {
                $address = Utility::parseUint($address[0], 32);
            }
        }

        if (is_integer($address) || is_float($address)) {
            $address = Utility::parseUint((int)$address, 32);
            $address = array(0xff & ($address >> 24) % 256, 0xff & ($address >> 16) % 256, 0xff & ($address >> 8) % 256, 0xff & $address % 256);
        }

        if (is_array($address) && count($address) == 4) {
            $this->address = array_map(function ($i) {
                return Utility::parseUint($i, 8);
            }, $address);
            return;
        }
        throw new InvalidFormatException("Invalid IP address specified");
    }

    /**
     * Get the default 'dotted-quad' representation of the IPv4 address
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf("%u.%u.%u.%u", $this->address[0], $this->address[1], $this->address[2], $this->address[3]);
    }

    /**
     * @return int
     */
    public function version()
    {
        return self::IPv4;
    }

    /**
     * Get uint32 representation of the IP address
     *
     * @return int
     */
    public function int()
    {
        // Go through the pack-unpack thing so php 32 bit doesn't turn 255.255.255.255 into a float
        $data = unpack("Nint", pack("CCCC", $this->address[0], $this->address[1], $this->address[2], $this->address[3]));
        return $data['int'];
    }

    /**
     * Returns whether the address is part of the private address pool (RFC 1918)
     *
     * @return bool
     */
    public function isPrivate()
    {
        /** @var $subnets Subnet[] */
        $subnets = array(
            Subnet::fromCidr(new Address("10.0.0.0"), 8),
            Subnet::fromCidr(new Address("172.16.0.0"), 12),
            Subnet::fromCidr(new Address("192.168.0.0"), 16),
        );
        foreach($subnets as $subnet) {
            if($subnet->contains($this)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->address[$offset]);
    }

    /**
     * @param mixed $offset
     * @return int
     */
    public function offsetGet($offset)
    {
        return $this->address[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws \InvalidArgumentException
     */
    public function offsetSet($offset, $value)
    {
        if (!isset($this->address[$offset])) {
            throw new \InvalidArgumentException("Invalid offset for setting IPv4 address");
        }
        if (!is_integer($value) || $value < 0 || $value > 255) {
            throw new \InvalidArgumentException("Invalid argument for setting IPv4 address");
        }
        $this->address[$offset] = $value;
    }

    /**
     * @param mixed $offset
     * @throws \BadMethodCallException
     */
    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException("Can unset parts of IPv4 address");
    }

    /**
     * Get the class of the IP address (for classful routing)
     */
    public function getClass()
    {
        if (($this->address[0] & 0x80) == 0) {
            return self::CLASS_A;
        }

        if (($this->address[0] & 0xC0) == 0x80) {
            return self::CLASS_B;
        }

        if (($this->address[0] & 0xE0) == 0xC0) {
            return self::CLASS_C;
        }

        if (($this->address[0] & 0xF0) == 0xE0) {
            return self::CLASS_D;
        }

        if (($this->address[0] & 0xF0) == 0xF0) {
            return self::CLASS_E;
        }

        // @codeCoverageIgnoreStart
        throw new UnknownClassException();
        // @codeCoverageIgnoreEnd
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

        return $address == $this;
    }
}
