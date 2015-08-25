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
 * A basic collection that uses an array for handling its items.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 * @package System\Collections
 */
abstract class ArrayCollectionBase extends EnumerableBase {
    /**
     * @var array
     */
    protected $_items;
    /**
     * @var int
     */
    private $_key = 0;


    /**
     * {@inheritDoc}
     */
    public final function count() {
        return \count($this->_items);
    }

    /**
     * {@inheritDoc}
     */
    public function current() {
        $result = $this->_items[$this->_key];
        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function elementAtOrDefault($index, $defValue = null) {
        if (\array_key_exists($index, $this->_items)) {
            return $this->_items[$index];
        }

        return $defValue;
    }

    /**
     * Returns the value that is used to represent an EOF key.
     *
     * @return mixed The EOF key.
     */
    protected function getEOFKey() {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function key() {
        return $this->valid() ? $this->_key
                              : $this->getEOFKey();
    }

    /**
     * {@inheritDoc}
     */
    public final function next() {
        if ($this->_key >= \count($this->_items)) {
            $this->throwException("No more items available!");
        }

        ++$this->_key;
    }

    /**
     * {@inheritDoc}
     */
    public final function reset() {
        $this->_key = 0;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize() {
        return \json_encode($this->_items);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized) {
        $this->_items = \json_decode($serialized, true);
    }

    /**
     * {@inheritDoc}
     */
    public final function valid() {
        return $this->_key < \count($this->_items);
    }
}
