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
 * A common collection / list.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 * @package System\Collections
 */
final class Collection extends ArrayCollectionBase implements IList {
    private $_equalityComparer;


    /**
     * Initializes a new instance of that class.
     *
     * @param mixed $items The initial items.
     *                     If there is only one argument and the value is callable, it
     *                     is used as key comparer.
     * @param callable $equalityComparer The optional key comparer.
     */
    public function __construct($items = null, $equalityComparer = null) {
        $this->_equalityComparer = static::getEqualComparerSafe($equalityComparer);

        $this->clear();
        if (!\is_null($items)) {
            $this->addRange($items);
        }

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
    public function addRange($items) {
        $i = static::asIterator($items, true);

        while ($i->valid()) {
            $this->_items[] = $i->current();

            $i->next();
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
    public function containsItem($item) {
        return $this->indexOf($item) > -1;
    }

    /**
     * {@inheritDoc}
     */
    public function indexOf($item) {
        $index = -1;
        foreach ($this->_items as $i) {
            ++$index;

            if ($this->compareItems($item, $i)) {
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
    public function insert($index, $item) {
        if (!$this->offsetExists($index)) {
            $this->throwIndexOutOfRange($index);
        }

        \array_splice($this->_items, $index, 0, array($item));
    }

    /**
     * {@inheritDoc}
     */
    public function isFixedSize() {
        return $this->isReadOnly();
    }

    /**
     * {@inheritDoc}
     */
    public function isReadOnly() {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isSynchronized() {
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
        if (\is_null($index)) {
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
    public function remove($item) {
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
    public function removeAt($index) {
        if ($this->offsetExists($index)) {
            \array_splice($this->_items, $index, 1);
            return;
        }

        $this->throwIndexOutOfRange($index);
    }

    private function throwIndexOutOfRange($index) {
        $this->throwException(\sprintf("Index '%s' not found!",
                                       $index));
    }
}
