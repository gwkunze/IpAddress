<?php
/**
 * Copyright (c) 2013 Gijs Kunze
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IPAddress\Test\IPv4;

use IPAddress\Exceptions\InvalidFormatException;
use IPAddress\IPv4\Address as IPv4Address;
use IPAddress\IPv4\Subnet as IPv4Subnet;

class SubnetTest extends \PHPUnit_Framework_TestCase
{
    public function testToString()
    {
        $subnet = new IPv4Subnet(new IPv4Address("10.0.0.1"), new IPv4Address("255.0.0.0"));
        $this->assertEquals("10.0.0.1 netmask 255.0.0.0", $subnet);

        $subnet = IPv4Subnet::fromCidr(new IPv4Address("192.168.3.1"), 24);
        $this->assertEquals("192.168.3.1 netmask 255.255.255.0", $subnet);
    }

    public function testContains()
    {
        $subnet = IPv4Subnet::fromCidr(new IPv4Address("192.168.3.1"), 24);
        $this->assertFalse($subnet->contains(new IPv4Address("192.168.2.255")));
        $this->assertTrue($subnet->contains(new IPv4Address("192.168.3.0")));
        $this->assertTrue($subnet->contains(new IPv4Address("192.168.3.255")));
        $this->assertFalse($subnet->contains(new IPv4Address("192.168.4.0")));
    }

    /**
     * @dataProvider subnetAttributes
     *
     * @param IPv4Subnet $subnet
     * @param $is_network
     * @param $is_broadcast
     */
    public function testSubnetAttributes(IPv4Subnet $subnet, $is_network, $is_broadcast)
    {
        $this->assertEquals($is_network, $subnet->isNetworkAddress());
        $this->assertEquals($is_broadcast, $subnet->isBroadCastAddress());
    }

    public function subnetAttributes()
    {
        return array(
            array(new IPv4Subnet("192.168.0.0", "255.255.0.0"), true, false),
            array(new IPv4Subnet("192.168.0.255", "255.255.0.0"), false, false),
            array(new IPv4Subnet("192.168.0.255", "255.255.255.0"), false, true),
            array(new IPv4Subnet("192.168.255.255", "255.255.0.0"), false, true),
        );
    }

    /**
     * @dataProvider subnetFromString
     *
     * @param $string
     * @param $expected
     */
    public function testFromString($string, $expected)
    {
        $subnet = IPv4Subnet::fromString($string);

        $this->assertEquals($expected, (string)$subnet);
    }

    public function subnetFromString()
    {
        return array(
            array("10.0.0.1 255.0.0.0", "10.0.0.1 netmask 255.0.0.0"),
            array("10.0.0.1netmask255.0.0.0", "10.0.0.1 netmask 255.0.0.0"),
            array("10.0.0.1nm255.0.0.0", "10.0.0.1 netmask 255.0.0.0"),
            array("10.0.0.1 nm 255.0.0.0", "10.0.0.1 netmask 255.0.0.0"),
            array("10.0.0.1/8", "10.0.0.1 netmask 255.0.0.0"),
        );
    }

    /**
     * @expectedException IPAddress\Exceptions\InvalidFormatException
     */
    public function testFromStringFail()
    {
        IPv4Subnet::fromString("foo");
    }

    /**
     * @dataProvider subnetMatch
     *
     * @param IPv4Subnet $subnet
     * @param IPv4Address $address
     * @param $match
     */
    public function testMatch(IPv4Subnet $subnet, $address, $match)
    {
        $this->assertEquals($match, $subnet->match($address));
    }

    public function subnetMatch()
    {
        return array(
            array(IPv4Subnet::fromString("10.0.0.0/8"), new IPv4Address("10.23.54.255"), true),
            array(IPv4Subnet::fromString("10.0.0.0/8"), new IPv4Address("10.0.0.0"), true),
            array(IPv4Subnet::fromString("10.0.0.0/8"), new IPv4Address("10.255.255.255"), true),
            array(IPv4Subnet::fromString("10.0.0.0/8"), new IPv4Address("11.0.0.0"), false),
            array(IPv4Subnet::fromString("10.0.0.0/8"), new IPv4Address("9.255.255.255"), false),
            array(IPv4Subnet::fromString("10.0.0.0/8"), "10.0.2.3", false),
        );
    }

}
