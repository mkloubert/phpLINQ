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


use \System\Linq\Enumerable;


/**
 * A common hashtable / dictionary (PHP 5.3).
 *
 * @package System\Collections
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
final class Dictionary extends ArrayCollectionBase implements IDictionary {
    private $_keyEqualityComparer;


    /**
     * Initializes a new instance of that class.
     *
     * @param mixed $items The initial items.
     *                     If there is only one argument and the value is callable, it
     *                     is used as key equality comparer.
     * @param callable $keyEqualityComparer The optional key equality comparer.
     */
    public function __construct($items = null, $keyEqualityComparer = null) {
        if (1 == \func_num_args()) {
            if (\is_callable($items)) {
                $keyEqualityComparer = $items;
                $items               = null;
            }
        }

        // keep sure to have an iterator
        $items = static::asIterator($items, true);

        $this->_keyEqualityComparer = static::getEqualComparerSafe($keyEqualityComparer);

        // copy to internal structure
        $this->_items = array();
        while ($items->valid()) {
            $this->add($items->key(),
                       $items->current());

            $items->next();
        }

        $this->reset();
    }


    /**
     * {@inheritDoc}
     */
    public function add($key, $value) {
        if ($this->containsKey($key)) {
            $this->throwException('Key already exists.');
        }

        $newEntry        = new \stdClass();
        $newEntry->key   = $key;
        $newEntry->value = $value;

        $this->_items[] = $newEntry;
    }

    /**
     * {@inheritDoc}
     */
    public function appendToArray(array &$arr, $withKeys = false) {
        while ($this->valid()) {
            $item = $this->current();

            if (!$withKeys) {
                $arr[] = $item->value();
            }
            else {
                $arr[$item->key()] = $item->value();
            }

            $this->next();
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function clear() {
        $this->_items = array();
    }

    private function compareKeys($x, $y) {
        return \call_user_func($this->_keyEqualityComparer,
                               $x, $y);
    }

    /**
     * {@inheritDoc}
     */
    public function containsKey($key) {
        foreach ($this->keys() as $dictKey) {
            if ($this->compareKeys($dictKey, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function current() {
        if (!$this->valid()) {
            return;
        }

        return self::objectToEntry(parent::current());
    }

    /**
     * {@inheritDoc}
     */
    public function elementAtOrDefault($index, $defValue = null) {
        if (\array_key_exists($index, $this->_items)) {
            return self::objectToEntry($this->_items[$index]);
        }

        return $defValue;
    }

    private function indexOfByOffset($offset) {
        foreach ($this->_items as $index => $item) {
            if ($this->compareKeys($item->key, $offset)) {
                return $index;
            }
        }

        return false;
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
    public function key() {
        return $this->valid() ? $this->current()->key()
                              : $this->getEOFKey();
    }

    /**
     * {@inheritDoc}
     */
    public function keys() {
        return Enumerable::create($this->_items)
                         ->select(function($x) {
                                      return $x->key;
                                  });
    }

    private static function objectToEntry(\stdClass $obj) {
        return new DictionaryEntry($obj->key,
                                   $obj->value);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset) {
        return $this->containsKey($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset) {
        $i = $this->indexOfByOffset($offset);
        if (false !== $i) {
            return $this->_items[$i]->value;
        }

        $this->throwException('Key not found!');
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value) {
        $doAdd = false;
        if (\is_null($offset)) {
            $doAdd = true;

            // find next index
            $offset = $this->count();
            while ($this->containsKey($offset)) {
                ++$offset;
            }
        }

        $i = $this->indexOfByOffset($offset);
        if (!$doAdd && (false !== $i)) {
            $this->_items[$i]->value = $value;
        }
        else {
            $this->add($offset, $value);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset) {
        $this->removeKey($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($key) {
        return $this->removeKey($key);
    }

    /**
     * {@inheritDoc}
     */
    public function removeKey($key) {
        $i = $this->indexOfByOffset($key);
        if (false !== $i) {
            \array_splice($this->_items, $i, 1);
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized) {
        $this->_items = \json_decode($serialized, false);
    }

    /**
     * {@inheritDoc}
     */
    public function values() {
        return Enumerable::create($this->_items)
                         ->select(function($x) {
                                      return $x->value;
                                  });
    }
}
