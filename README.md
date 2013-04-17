# Ip Address  [![Build Status](https://travis-ci.org/gwkunze/IpAddress.png)](https://travis-ci.org/gwkunze/IpAddress)

This library allows you to handle IP addresses in various forms (currently IPv4 only) and test whether an IP matches
certain constraints (equality, in a subnet or in a range).

Example
=======

``` php
<?php

use IPAddress\IPv4\Address;
use IPAddress\IPv4\Subnet;

$address = new Address("1.2.8.200"); // These statements yield the same address
$address = new Address(0x010208c8);
$address = new Address(array(1, 2, 8, 200));
$address = new Address(array("1", "2", "010", "0xc8"));

$subnet = new Subnet("1.2.3.4", "255.255.0.0"); // These statements yield the same subnet
$subnet = Subnet::fromCidr("1.2.3.4", 16);
$subnet = Subnet::fromString("1.2.3.4 255.255.0.0");
$subnet = Subnet::fromString("1.2.3.4/16");

if($address->isPrivate()) {
    echo "Your address is in one of the RFC1918 Private networks.\n";
}

if($subnet->match($address)) {
    echo "I know your address is in the \"$subnet\" subnet.\n";
    // OUTPUT: I know your address is in the "1.2.3.4 netmask 255.255.0.0" subnet.
}

```

The library's test have been run on both 32-bit and 64-bit php and should work on both. Please note that the Address class' int() method (which returns the integer representation of an IP address) will return a negative number for ip addresses larger than 127.255.255.255 on 32-bit php due to the fact that php doesn't support unsigned integers.

TODO
====

 - More utility methods
 - IPv6 support

Disclaimer
==========

I have limited knowledge of IP addresses so I could have made some mistakes regarding terminology or even behavior of the library in certain cases.

License
=======

MIT, see LICENSE
