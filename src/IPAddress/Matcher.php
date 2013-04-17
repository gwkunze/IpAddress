<?php
/**
 * Copyright (c) 2013 Gijs Kunze
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IPAddress;


interface Matcher {
    /**
     * Tests whether the given address matches
     *
     * @param $address
     * @return bool
     */
    public function match($address);
}