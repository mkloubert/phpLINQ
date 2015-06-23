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


namespace System\Collections;


/**
 * Describes a hashtable / dictionary.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 * @package System\Collections
 */
interface IDictionary extends \ArrayAccess, IEnumerable {
    /**
     * Adds a new entry.
     *
     * @param mixed $key The key.
     * @param mixed $value The value.
     */
    function add($key, $value);

    /**
     * Removes all entries.
     */
    function clear();

    /**
     * Checks if a key exists or not.
     *
     * @param mixed $key The kex to check.
     *
     * @return bool Key exsists or not.
     */
    function containsKey($key);

    /**
     * Gets a value indicating whether the dictionary object has a fixed size.
     *
     * @return bool Has a fixed size or not.
     */
    function isFixedSize();

    /**
     * Gets a value indicating whether the dictionary object is read-only.
     *
     * @return bool Is read-only or not.
     */
    function isReadOnly();

    /**
     * Gets a value indicating whether the dictionary object is thread-safe.
     *
     * @return bool Is synchronized or not.
     */
    function isSynchronized();

    /**
     * Returns all keys of that dictionary.
     *
     * @return IEnumerable The keys.
     */
    function keys();

    /**
     * @see \System\Collections\IDictionary::removeKey()
     */
    function remove($key);

    /**
     * Removes an entry by key.
     *
     * @param mixed $key The key.
     *
     * @return bool Entry was removed or not.
     */
    function removeKey($key);

    /**
     * Returns all values of that dictionary.
     *
     * @return IEnumerable The values.
     */
    function values();
}
