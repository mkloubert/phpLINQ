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

use \System\ArgumentOutOfRangeException;


/**
 * A common collection / list.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 * @package System\Collections
 */
class Collection extends ArrayCollectionBase implements IList {
    private $_equalityComparer;


    /**
     * Initializes a new instance of that class.
     *
     * @param mixed $items The initial items.
     * @param callable $equalityComparer The optional key comparer.
     */
    public function __construct($items = null, $equalityComparer = null) {
        $this->_equalityComparer = static::getEqualityComparerSafe($equalityComparer);

        $this->clear();
        $this->addRange($items);
        $this->reset();
    }


    /**
     * {@inheritDoc}
     */
    public function add($item) {
        $this->_items[] = $item;

        return $this->count() - 1;
    }

    /**
     * {@inheritDoc}
     */
    public function addItems() {
        $this->addRange(\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function addRange($items = null) {
        foreach (\func_get_args() as $arg) {
            $i = static::asIterator($arg, true);

            while ($i->valid()) {
                $this->_items[] = $i->current();

                $i->next();
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function clear() {
        $this->_items = array();
    }

    private function compareItems($x, $y) {
        return \call_user_func($this->_equalityComparer,
                               $x, $y);
    }

    /**
     * {@inheritDoc}
     */
    public function containsItem($item) : bool {
        return $this->indexOf($item) > -1;
    }

    /**
     * {@inheritDoc}
     */
    public function indexOf($item) : int {
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
    public function insert(int $index, $item) {
        if ($index === $this->count()) {
            $this->add($item);
            return;
        }

        if (!$this->offsetExists($index)) {
            $this->throwIndexOutOfRange($index);
        }

        \array_splice($this->_items, $index, 0, array($item));
    }

    /**
     * {@inheritDoc}
     */
    public function isFixedSize() : bool {
        return $this->isReadOnly();
    }

    /**
     * {@inheritDoc}
     */
    public function isReadOnly() : bool {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isSynchronized() : bool {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($index) {
        return \array_key_exists($index, $this->_items);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($index) {
        if ($this->offsetExists($index)) {
            return $this->_items[$index];
        }

        $this->throwIndexOutOfRange($index);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($index, $value) {
        if (null === $index) {
            $this->add($value);
            return;
        }

        if (!$this->offsetExists($index)) {
            $this->throwIndexOutOfRange($index);
        }

        $this->_items[$index] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($index) {
        $this->removeAt($index);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($item) : bool {
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
    public function removeAt(int $index) {
        if ($this->offsetExists($index)) {
            \array_splice($this->_items, $index, 1);
            return;
        }

        $this->throwIndexOutOfRange($index);
    }

    private function throwIndexOutOfRange($index) {
        throw new ArgumentOutOfRangeException('index', $index, 'Index not found!');
    }
}
