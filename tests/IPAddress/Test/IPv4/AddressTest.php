<?php
/**
 * Copyright (c) 2013 Gijs Kunze
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IPAddress\Test\IPv4;

use IPAddress\IPv4\Address as IPv4Address;
use IPAddress\IPv4\Subnet as IPv4Subnet;

/**
 * Class IPv4Test
 *
 * Tests for the IPv4Address class
 *
 * @package IPAddress\Test
 */
class AddressTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider invalidIPs
     * @expectedException \IPAddress\Exceptions\InvalidFormatException
     *
     * @param $ip_address
     */
    public function testInvalidIps($ip_address)
    {
        new IPv4Address($ip_address);
    }

    /**
     * @dataProvider validIPs
     *
     * @param $ip
     * @param $dotted_quad
     */
    public function testToString($ip, $dotted_quad)
    {
        $this->assertEquals($dotted_quad, (string)(new IPv4Address($ip)));
    }

    /**
     * @dataProvider validIPs
     *
     * @param $ip
     * @param $dotted_quad
     * @param $integer
     */
    public function testInteger($ip, $dotted_quad, $integer)
    {
        $ip = new IPv4Address($ip);
        $int = $ip->int();

        $this->assertEquals($integer, $int);
    }

    public function testVersion()
    {
        $ip = new IPv4Address("1.2.3.4");

        $this->assertEquals(IPv4Address::IPv4, $ip->version());
    }

    public function testArrayAccess()
    {
        $ip = new IPv4Address("10.20.30.40");

        $this->assertEquals(10, $ip[0]);
        $this->assertEquals(20, $ip[1]);
        $this->assertEquals(30, $ip[2]);
        $this->assertEquals(40, $ip[3]);

        $ip[0] = 50;
        $this->assertEquals("50.20.30.40", (string)$ip);

        $this->assertTrue(isset($ip[1]));
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testCantUnset()
    {
        $ip = new IPv4Address("10.20.30.40");

        unset($ip[0]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidIndex()
    {
        $ip = new IPv4Address("10.20.30.40");

        $ip["foo"] = 3;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidValue()
    {
        $ip = new IPv4Address("10.20.30.40");

        $ip[0] = "foo";
    }

    /**
     * @dataProvider ipClasses
     */
    public function testClass($ip, $class)
    {
        $ip = new IPv4Address($ip);

        $this->assertEquals($class, $ip->getClass());
    }

    /**
     * @dataProvider privateIpAddresses
     */
    public function testPrivate($ip, $private)
    {
        $ip = new IPv4Address($ip);

        $this->assertEquals($private, $ip->isPrivate());
    }

    /**
     * @dataProvider matchAddresses
     */
    public function testMatch(IPv4Address $ip1, $ip2, $equal)
    {
        $this->assertEquals($equal, $ip1->match($ip2));
    }

    public function invalidIPs()
    {
        return array(
            array("1.2"),
            array("1.2.3"),
            array("1.2.3.4.5"),
            array("::1"),
            array("1234:1234:1234:1234:1234:1234:1234:1234"),
            array("foo"),
            array("255.255.256.255"),
            array(array()),
            array(array(1, 2, 3, 4, 5)),
            array(array(-1)),
            array(array(10,20,30,300)),
            array(array("10", "20", "30", "300")),
        );
    }

    public function validIPs()
    {
        return array(
            array("0.0.0.0", "0.0.0.0", 0),
            array("1.2.3.4", "1.2.3.4", 16909060),
            array("01.01.01.01", "1.1.1.1", 16843009),
            array(array(1, 2, 3, 4), "1.2.3.4", 16909060),
            array(array("1", "2", "3", "4"), "1.2.3.4", 16909060),
            array("0x01.0x02.0x0A.0x0B", "1.2.10.11", 16910859),
            array(array("0x01", "0x02", "0x0A", "0x0B"), "1.2.10.11", 16910859),
            array("01.010.011.012", "1.8.9.10", 17303818),
            array(array("01", "02", "010", "011"), "1.2.8.9", 16910345),
            array("0x01020A0B", "1.2.10.11", 16910859),
            array("0100405013", "1.2.10.11", 16910859),
            array(4294967295, "255.255.255.255", (int)0xffffffff),
            array("4294967295", "255.255.255.255", (int)0xffffffff),
            array("1", "0.0.0.1", 1),
        );
    }

    public function ipClasses()
    {
        return array(
            array("0.0.0.0", IPv4Address::CLASS_A),
            array("127.255.255.255", IPv4Address::CLASS_A),
            array("128.0.0.0", IPv4Address::CLASS_B),
            array("191.255.255.255", IPv4Address::CLASS_B),
            array("192.0.0.0", IPv4Address::CLASS_C),
            array("223.255.255.255", IPv4Address::CLASS_C),
            array("224.0.0.0", IPv4Address::CLASS_D),
            array("239.255.255.255", IPv4Address::CLASS_D),
            array("240.0.0.0", IPv4Address::CLASS_E),
            array("255.255.255.255", IPv4Address::CLASS_E),
        );
    }

    public function privateIpAddresses()
    {
        return array(
            array("9.255.255.255", false),
            array("10.0.0.0", true),
            array("10.0.1.0", true),
            array("10.1.0.0", true),
            array("10.255.0.0", true),
            array("10.255.255.255", true),
            array("11.0.0.0", false),

            array("172.15.255.255", false),
            array("172.16.0.0", true),
            array("172.31.255.255", true),
            array("172.32.0.0", false),

            array("192.167.255.255", false),
            array("192.168.0.0", true),
            array("192.168.255.255", true),
            array("192.169.0.0", false),
        );
    }

    public function matchAddresses()
    {
        return array(
            array(new IPv4Address("255.255.255.255"), new IPv4Address(array("0xff", "0xff", 255, 0377)), true),
            array(new IPv4Address("10.0.0.1"), new IPv4Address("10.0.0.1"), true),
            array(new IPv4Address("10.0.0.1"), new IPv4Address("10.0.0.2"), false),
            array(new IPv4Address("255.255.255.255"), new IPv4Address("12.12.12.12"), false),
            array(new IPv4Address("192.168.0.1"), new IPv4Subnet("192.168.0.1", "255.255.255.0"), false),
            array(new IPv4Address("192.168.0.1"), "foo", false),
        );
    }
}
