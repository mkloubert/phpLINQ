<?php

/**
 *  LINQ concept for PHP
 *  Copyright (C) 2015  Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 *
 *    This library is free software; you can redistribute it and/or
 *    modify it under the terms of the GNU Lesser General Public
 *    License as published by the Free Software Foundation; either
 *    version 3.0 of the License, or (at your option) any later version.
 *
 *    This library is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 *    Lesser General Public License for more details.
 *
 *    You should have received a copy of the GNU Lesser General Public
 *    License along with this library.
 */


namespace System\Linq;

use \System\Collections\IEnumerable;


/**
 * Describes an object that defines an indexer, counter and boolean search
 * method for data structures that map keys to sequences of values.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 * @package System\Linq
 */
interface ILookup extends \Countable, \ArrayAccess, IEnumerable {
    /**
     * Checks if a key exists in that lookup or not.
     *
     * @param mixed $key The key to check.
     *
     * @return boolean Exists or not.
     */
    function containsKey($key);
}
