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
 * Describes a list.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 * @package System\Collections
 */
interface IList extends \ArrayAccess, IEnumerable {
    /**
     * Adds a new item.
     *
     * @param mixed $item The item to add.
     *
     * @return integer The index of the new item.
     */
    function add($item);

    /**
     * Adds a list of items.
     */
    function addItems();

    /**
     * Adds a range of items.
     *
     * @param Traversable|array $items The items to add.
     */
    function addRange($items);

    /**
     * Removes all items.
     */
    function clear();

    /**
     * Checks if the list contains an item.
     *
     * @param mixed $item The item to check.
     *
     * @return boolean Contains item or not.
     */
    function containsItem($item);

    /**
     * Returns the index of the first occurence of a value / item.
     *
     * @param mixed $item The item to search for.
     *
     * @return integer The zero based index or -1 if not found.
     */
    function indexOf($item);

    /**
     * Inserts an item into that list.
     *
     * @param integer $index The index where the item should be inserted.
     * @param mixed $item The item to insert.
     */
    function insert($index, $item);

    /**
     * Gets a value indicating whether the list object has a fixed size.
     */
    function isFixedSize();

    /**
     * Gets a value indicating whether the list object is read-only.
     */
    function isReadOnly();

    /**
     * Gets a value indicating whether the list object is thread-safe.
     */
    function isSynchronized();

    /**
     * Removes an item.
     *
     * @param mixed $item The item to remove.
     *
     * @return boolean The item was removed or not.
     */
    function remove($item);

    /**
     * Removes an item at a specific position.
     *
     * @param integer $index The zero based index.
     */
    function removeAt($index);
}
