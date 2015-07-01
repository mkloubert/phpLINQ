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
 * Describes a set.
 *
 * @package System\Collections
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
interface ISet extends IEnumerable {
    /**
     * Adds a new item.
     *
     * @param mixed $item The item to add.
     *
     * @return bool Item was added or not.
     */
    function add($item);

    /**
     * Removes all items.
     */
    function clear();

    /**
     * Checks if the set contains an item.
     *
     * @param mixed $item The item to check.
     *
     * @return bool Contains item or not.
     */
    function containsItem($item);

    /**
     * Gets a value indicating whether the set object is read-only.
     *
     * @return bool Is read-only or not.
     */
    function isReadOnly();

    /**
     * Removes an item.
     *
     * @param mixed $item The item to remove.
     *
     * @return bool The item was removed or not.
     */
    function remove($item);
}
