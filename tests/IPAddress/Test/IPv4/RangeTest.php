<?php
/**
 * Copyright (c) 2013 Gijs Kunze
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IPAddress\Test\IPv4;

use IPAddress\IPv4\Range as IPv4Range;
use IPAddress\IPv4\Address as IPv4Address;

class RangeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider toStringRanges
     *
     * @param $ip1
     * @param $ip2
     * @param $expected
     */
    public function testToString($ip1, $ip2, $expected)
    {
        $range = new IPv4Range($ip1, $ip2);

        $this->assertEquals($expected, (string)$range);
    }

    public function toStringRanges()
    {
        return array(
            array("10.0.0.0", "10.0.3.6", "10.0.0.0-10.0.3.6"),
            array("10.0.3.6", "10.0.0.0", "10.0.0.0-10.0.3.6"),
            array("10.0.0.0", "10.0.0.0", "10.0.0.0-10.0.0.0"),
            array("255.255.255.254", "255.255.255.255", "255.255.255.254-255.255.255.255"),
            array("255.255.255.255", "255.255.255.254", "255.255.255.254-255.255.255.255"),
        );
    }

    /**
     * @dataProvider fromString
     *
     * @param $string
     * @param $expected
     */
    public function testFromString($string, $expected)
    {
        $range = IPv4Range::fromString($string);

        $this->assertEquals($expected, (string)$range);
    }

    public function fromString()
    {
        return array(
            array("10.0.3.6 - 10.0.0.0", "10.0.0.0-10.0.3.6"),
            array("10.0.0.0 - 10.0.3.6", "10.0.0.0-10.0.3.6"),
            array("200.0.0.0 -255.255.255.253", "200.0.0.0-255.255.255.253"),
        );
    }

    public function testAccess()
    {
        $range = new IPv4Range("10.0.0.20", "10.0.0.5");

        $this->assertEquals("10.0.0.5", (string)$range->ip1());
        $this->assertEquals("10.0.0.20", (string)$range->ip2());
    }

    /**
     * @dataProvider matchRanges
     *
     * @param IPv4Range $range
     * @param $ip
     * @param $match
     */
    public function testMatch(IPv4Range $range, $ip, $match)
    {
        $this->assertEquals($match, $range->match($ip));
    }

    public function matchRanges()
    {
        return array(
            array(IPv4Range::fromString("10.0.0.1-10.0.0.3"), new IPv4Address("10.0.0.0"), false),
            array(IPv4Range::fromString("10.0.0.1-10.0.0.3"), new IPv4Address("10.0.0.1"), true),
            array(IPv4Range::fromString("10.0.0.1-10.0.0.3"), new IPv4Address("10.0.0.2"), true),
            array(IPv4Range::fromString("10.0.0.1-10.0.0.3"), new IPv4Address("10.0.0.3"), true),
            array(IPv4Range::fromString("10.0.0.1-10.0.0.3"), new IPv4Address("10.0.0.4"), false),
            array(IPv4Range::fromString("10.0.0.1-10.0.0.3"), "foo", false),
        );
    }

    /**
     * @expectedException IPAddress\Exceptions\InvalidFormatException
     */
    public function testInvalidString()
    {
        IPv4Range::fromString("foo");
    }
}