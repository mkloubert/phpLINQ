<?php

/**********************************************************************************************************************
 * phpLINQ (https://github.com/mkloubert/phpLINQ)                                                                     *
 *                                                                                                                    *
 * Copyright (c) 2015, Marcel Joachim Kloubert <marcel.kloubert@gmx.net>                                              *
 * All rights reserved.                                                                                               *
 *                                                                                                                    *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the   *
 * following conditions are met:                                                                                      *
 *                                                                                                                    *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the          *
 *    following disclaimer.                                                                                           *
 *                                                                                                                    *
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the       *
 *    following disclaimer in the documentation and/or other materials provided with the distribution.                *
 *                                                                                                                    *
 * 3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote    *
 *    products derived from this software without specific prior written permission.                                  *
 *                                                                                                                    *
 *                                                                                                                    *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, *
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE  *
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, *
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR    *
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,  *
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE   *
 * USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.                                           *
 *                                                                                                                    *
 **********************************************************************************************************************/

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
     *
     * @param mixed ...$item One or more item to add.
     */
    function addItems();

    /**
     * Adds a range of items.
     *
     * @param mixed ...$items One or more item list to add.
     */
    function addRange($items = null);

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
    function containsItem($item) : bool;

    /**
     * Returns the index of the first occurence of a value / item.
     *
     * @param mixed $item The item to search for.
     *
     * @return int The zero based index or -1 if not found.
     */
    function indexOf($item) : int;

    /**
     * Inserts an item into that list.
     *
     * @param int $index The index where the item should be inserted.
     * @param mixed $item The item to insert.
     */
    function insert(int $index, $item);

    /**
     * Gets a value indicating whether the list object has a fixed size.
     *
     * @return bool The read-only or not.
     */
    function isFixedSize() : bool;

    /**
     * Gets a value indicating whether the list object is read-only.
     *
     * @return bool The read-only or not.
     */
    function isReadOnly() : bool;

    /**
     * Gets a value indicating whether the list object is thread-safe.
     *
     * @return bool The synchronized or not.
     */
    function isSynchronized() : bool;

    /**
     * Removes an item.
     *
     * @param mixed $item The item to remove.
     *
     * @return bool The item was removed or not.
     */
    function remove($item) : bool;

    /**
     * Removes an item at a specific position.
     *
     * @param int $index The zero based index.
     */
    function removeAt(int $index);
}
