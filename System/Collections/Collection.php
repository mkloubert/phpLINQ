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

use \System\ArgumentException;
use \System\ArgumentOutOfRangeException;
use \System\Collections\ObjectModel\ReadOnlyCollection;


/**
 * A common collection / list.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 * @package System\Collections
 */
class Collection extends ArrayCollectionBase implements IList {
    private $_equalityComparer;
    private $_itemValidator;


    /**
     * Initializes a new instance of that class.
     *
     * @param mixed $items The initial items.
     * @param callable $equalityComparer The optional key comparer.
     * @param callable $itemValidator The custom item validator to use.
     */
    public function __construct($items = null, $equalityComparer = null, $itemValidator = null) {
        $items                   = static::asIterator($items, true);
        $this->_equalityComparer = static::getEqualityComparerSafe($equalityComparer);
        $this->_itemValidator    = static::getValueValidatorSafe($itemValidator);

        $this->clearInner();

        while ($items->valid()) {
            $i = $items->current();
            $this->throwIfItemIsInvalid($i);

            $this->addInner($i);

            $items->next();
        }

        $this->reset();
    }


    /**
     * {@inheritDoc}
     */
    public final function add($item) : int {
        $this->throwIfFixedSize();
        $this->throwIfItemIsInvalid($item);

        return $this->addInner($item);
    }

    /**
     * @see Collection::add()
     */
    protected function addInner($item) : int {
        $this->_items[] = $item;

        return $this->count() - 1;
    }

    /**
     * {@inheritDoc}
     */
    public final function addItems() {
        $this->addRange(\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public final function addRange($items = null) {
        foreach (\func_get_args() as $arg) {
            $i = static::asIterator($arg, true);

            while ($i->valid()) {
                $this->add($i->current());

                $i->next();
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public final function asReadOnly() : IReadOnlyList {
        return !$this->isReadOnly() ? new ReadOnlyCollection($this->_items,
                                                             $this->_equalityComparer,
                                                             $this->_itemValidator)
                                    : $this;
    }

    /**
     * {@inheritDoc}
     */
    public final function clear() {
        $this->throwIfFixedSize();

        $this->clearInner();
    }

    /**
     * @see Collection::clear()
     */
    protected function clearInner() {
        $this->_items = [];
    }

    private function compareItems($x, $y) {
        return \call_user_func($this->_equalityComparer,
                               $x, $y);
    }

    /**
     * {@inheritDoc}
     */
    public final function containsItem($item) : bool {
        foreach (\func_get_args() as $itemToCheck) {
            $this->throwIfItemIsInvalid($itemToCheck);

            if ($this->indexOf($itemToCheck) < 0) {
                // not found
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public final function indexOf($item) : int {
        foreach ($this->_items as $index => $value) {
            if ($this->compareItems($item, $value)) {
                // found
                return $index;
            }
        }

        // not found
        return -1;
    }

    /**
     * {@inheritDoc}
     */
    public final function insert(int $index, $item) {
        $this->throwIfFixedSize();
        $this->throwIfItemIsInvalid($item);

        $this->insertInner($index, $item);
    }

    /**
     * @see Collection::insert()
     */
    protected function insertInner(int $index, $item) {
        if ($index === $this->count()) {
            $this->add($item);
            return;
        }

        if (!$this->offsetExists($index)) {
            $this->throwIndexOutOfRange($index);
        }

        \array_splice($this->_items, $index, 0, [$item]);
    }

    private function isItemValid($item) : bool {
        return \call_user_func($this->_itemValidator,
                               $item);
    }

    /**
     * {@inheritDoc}
     */
    public final function offsetExists($index) {
        return \array_key_exists($index, $this->_items);
    }

    /**
     * {@inheritDoc}
     */
    public final function offsetGet($index) {
        if ($this->offsetExists($index)) {
            return $this->_items[$index];
        }

        $this->throwIndexOutOfRange($index);
    }

    /**
     * {@inheritDoc}
     */
    public final function offsetSet($index, $value) {
        $this->throwIfItemIsInvalid($value);

        if (null === $index) {
            $this->add($value);
            return;
        }

        $this->throwIfReadOnly();

        if (!$this->offsetExists($index)) {
            $this->throwIndexOutOfRange($index);
        }

        $this->_items[$index] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public final function offsetUnset($index) {
        $this->removeAt($index);
    }

    /**
     * {@inheritDoc}
     */
    public final function remove($item) : bool {
        $this->throwIfFixedSize();

        $index = $this->indexOf($item);
        if ($index > -1) {
            $this->removeAt($index);
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public final function removeAt(int $index) {
        $this->throwIfFixedSize();

        $this->removeAtInner($index);
    }

    /**
     * @see Collection::removeAt()
     */
    protected function removeAtInner(int $index) {
        if ($this->offsetExists($index)) {
            \array_splice($this->_items, $index, 1);
            return;
        }

        $this->throwIndexOutOfRange($index);
    }

    /**
     * Throws an exception if an item is invalid.
     *
     * @param mixed $item The item to check.
     *
     * @throws ArgumentException Is invalid item.
     */
    protected final function throwIfItemIsInvalid($item) {
        if (!$this->isItemValid($item)) {
            throw new ArgumentException('item', 'Item is not valid!');
        }
    }

    private function throwIndexOutOfRange($index) {
        throw new ArgumentOutOfRangeException($index, 'index', 'Index not found!');
    }
}
