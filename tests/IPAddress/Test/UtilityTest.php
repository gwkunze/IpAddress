<?php
/**
 * Copyright (c) 2013 Gijs Kunze
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IPAddress\Test;


use IPAddress\Utility;

class UtilityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider invalidUints
     * @expectedException \IPAddress\Exceptions\InvalidFormatException
     *
     * @param $value
     * @param $bits
     */
    public function testInvalidNumbers($value, $bits)
    {
        Utility::parseUint($value, $bits);
    }

    /**
     * @dataProvider validUints
     *
     * @param $value
     * @param $bits
     * @param $number
     */
    public function testValidNumbers($value, $bits, $number)
    {
        $this->assertEquals($number, Utility::parseUint($value, $bits));
    }

    public function invalidUints() {
        return array(
            // Out of bounds errors
            array(-1, 10),
            array(1, 0),
            array(2, 1),
            array(4, 2),
            array(8, 3),
            // Invalid input
            array(array(), 8),
            array(1, -8),
            array("0xgg", 32),
            array("09", 8),
            array("foo", 8),
            // Go too far
            array(123, 65)
        );
    }

    public function validUints() {
        return array(
            array(0, 0, 0),
            array(255, 8, 255),
            array("255", 8, 255),
            array("0377", 8, 255),
            array("0xff", 8, 255),
            array("0XFF", 8, 255),
            array("0xffFFffFF", 32, (int)0xffffffff),
        );
    }
}
