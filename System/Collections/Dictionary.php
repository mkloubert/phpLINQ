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
 * A common hashtable / dictionary (PHP 5.3).
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
final class Dictionary extends EnumerableBase implements IDictionary {
    private $_items;
    private $_keyEqualityComparer;


    /**
     * Initializes a new instance of that class.
     *
     * @param mixed $items The initial items.
     *                     If there is only one argument and the value is callable, it
     *                     is used as key comparer.
     * @param callable $keyEqualityComparer The optional key comparer.
     */
    public function __construct($items = null, $keyEqualityComparer = null) {
        if (1 == func_num_args()) {
            if (is_callable($items)) {
                $keyEqualityComparer = $items;
                $items               = null;
            }
        }

        if (is_null($items)) {
            // initialize empty storage
            $items = array();
        }

        $this->_keyEqualityComparer = static::getEqualComparerSafe($keyEqualityComparer);

        // copy to internal structure
        $this->_items = array();
        foreach (static::asIterator($items) as $k => $v) {
            $this->add($k, $v);
        }

        $this->reset();
    }


    public function add($key, $value) {
        if ($this->containsKey($key)) {
            $this->throwException('Key already exists.');
        }

        $newEntry        = new \stdClass();
        $newEntry->key   = $key;
        $newEntry->value = $value;

        $this->_items[] = $newEntry;
    }

    public function clear() {
        $this->_items = array();
    }

    private function compareKeys($x, $y) {
        return call_user_func($this->_keyEqualityComparer,
                              $x, $y);
    }

    public function containsKey($key) {
        return $this->keys()
                    ->any(function($x, $ctx) use ($key) {
                              return $ctx->sequence
                                         ->compareKeys($x, $key);
                          });
    }

    public function count() {
        return count($this->_items);
    }

    public function current() {
        $i = $this->_i->current();
        if (is_object($i)) {
            return new DictionaryEntry($i->key, $i->value);
        }
    }

    public final function elementAtOrDefault($index, $defValue = null) {
        if (isset($this->_items[$index])) {
            $i = $this->_items[$index];

            return new DictionaryEntry($i->key, $i->value);
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

    public function isFixedSize() {
        return $this->isReadOnly();
    }

    public function isReadOnly() {
        return false;
    }

    public function isSynchronized() {
        return false;
    }

    public function keys() {
        return static::create($this->_items)
                     ->select(function($x) {
                                  return $x->key;
                              });
    }

    public function offsetExists($offset) {
        return $this->containsKey($offset);
    }

    public function offsetGet($offset) {
        $i = $this->indexOfByOffset($offset);
        if (false !== $i) {
            return $this->_items[$i]->value;
        }

        $this->throwException('Key not found!');
    }

    public function offsetSet($offset, $value) {
        $doAdd = false;
        if (is_null($offset)) {
            // find next index

            $doAdd = true;

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

    public function offsetUnset($offset) {
        $this->removeKey($offset);
    }

    public function remove($key) {
        return $this->removeKey($key);
    }

    public function removeKey($key) {
        $i = $this->indexOfByOffset($key);
        if (false !== $i) {
            array_splice($this->_items, $i, 1);
            return true;
        }

        return false;
    }

    public function rewind() {
        $this->_i = new \ArrayIterator($this->_items);
    }

    public function values() {
        return static::create($this->_items)
                     ->select(function($x) {
                                  return $x->value;
                              });
    }
}
