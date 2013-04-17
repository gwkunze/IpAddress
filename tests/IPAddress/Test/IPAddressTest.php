<?php
/**
 * Copyright (c) 2013 Gijs Kunze
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IPAddress\Test;


use IPAddress\IPAddress;

class IPAddressTest extends \PHPUnit_Framework_TestCase
{
    public function testFromString()
    {
        $ip = IPAddress::fromString("10.23.4.1");
        $this->assertInstanceOf("IPAddress\\IPv4\\Address", $ip);
        $this->assertEquals("10.23.4.1", (string)$ip);

        $subnet = IPAddress::fromString("10.0.0.0/24");
        $this->assertInstanceOf("IPAddress\\IPv4\\Subnet", $subnet);
        $this->assertEquals("10.0.0.0 netmask 255.255.255.0", (string)$subnet);

        $range = IPAddress::fromString("10.0.0.5 - 10.0.0.20");
        $this->assertInstanceOf("IPAddress\\IPv4\\Range", $range);
        $this->assertEquals("10.0.0.5-10.0.0.20", (string)$range);
    }

    /**
     * @expectedException IPAddress\Exceptions\InvalidFormatException
     */
    public function testFromStringFail()
    {
        IPAddress::fromString("foo");
    }
}