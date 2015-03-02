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

use \System\Collections\DictionaryEntry;
use \System\Collections\Generic\EnumerableBase;
use \System\Linq\Enumerable;


/**
 * A common hashtable / dictionary (PHP 5.3).
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
final class Dictionary extends EnumerableBase implements IDictionary {
    private $_items;
    private $_iterator;
    private $_keyComparer;
    
    
    /**
     * Initializes a new instance of that class.
     * 
     * @param array|\Iterator|callable $items The inital items.
     *                                        If there is only one argument
     *                                        and the value is callable, it
     *                                        is used as key comparer.
     * @param callable $keyComparer The optional key comparer.
     */
    public function __construct($items = null, $keyComparer = null) {
        if (1 == func_num_args()) {
            if (is_callable($items)) {
                $keyComparer = $items;
                $items = null;
            }
        }
        
        if (is_null($items)) {
            // initialize empty storage
            $items = array();
        }
        
        // keep sure to have an iterator
        if (is_array($items)) {
            $items = new \ArrayIterator($items);
        }
        
        // copy to internal structure
        $this->_items = array();
        foreach ($items as $k => $v) {
            $this->add($k, $v);
        }
        
        if (is_null($keyComparer)) {
            // default logic
            
            $keyComparer = function ($x, $y) {
                return $x == $y;
            };
        }
        
        $this->checkForFunctionOrThrow($keyComparer, 2, false);
        
        $this->_keyComparer = $keyComparer;
        
        $this->reset();
    }
    
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\IDictionary::add()
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
     * (non-PHPdoc)
     * @see \System\Collections\IDictionary::clear()
     */
    public function clear() {
        $this->_items = array();
    }
    
    // this needs to be public in PHP 5.3
    public function compareKeys($x, $y) {
        $kc = $this->_keyComparer;
        
        return $kc($x, $y);
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\IDictionary::containsKey()
     */
    public function containsKey($key) {
        $dict = $this;
        
        return $this->keys()
                    ->any(function($k) use ($key, $dict) {
                              return $dict->compareKeys($k, $key);
                          });
    }
    
    /**
     * (non-PHPdoc)
     * @see Countable::count()
     */
    public function count() {
        return count($this->_items);
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::current()
     */
    public function current() {
        $i = $this->_iterator->current();
        if (is_object($i)) {
            return new DictionaryEntry($i->key, $i->value);
        }
        
        return;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::elementAtOrDefault()
     */
    public final function elementAtOrDefault($index, $defValue = null) {
        if (isset($this->_items[$index])) {
            return $this->_items[$index]
                        ->value;
        }
        
        return $defValue;
    }
    
    /**
     * Creates a new instance from a list of values.
     * 
     * @return \System\Collections\Dictionary The new instance.
     */
    public static function fromValues() {
        return new static(func_get_args());
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
     * (non-PHPdoc)
     * @see \System\Collections\IDictionary::isFixedSize()
     */
    public function isFixedSize() {
        return $this->isReadOnly();
    }

    /**
     * (non-PHPdoc)
     * @see \System\Collections\IDictionary::isReadOnly()
     */
    public function isReadOnly() {
        return false;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\IDictionary::isSynchronized()
     */
    public function isSynchronized() {
        return false;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::key()
     */
    public function key() {
        return $this->_iterator->key();
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\IDictionary::keys()
     */
    public function keys() {
        return static::toEnumerable($this->keysInner());
    }
    
    private function keysInner() {
    	$result = array();
    	
        foreach ($this->_items as $i) {
            $result[] = $i->key;
        }
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::next()
     */
    public function next() {
        $this->_iterator->next();
    }
    
    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset) {
        return $this->containsKey($offset);
    }
    
    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset) {
        $i = $this->indexOfByOffset($offset);
        if (false !== $i) {
            return $this->_items[$i]->value;
        }
        
        $this->throwException('Key not found!');
    }
    
    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetSet()
     */
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
    
    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset) {
        $this->removeKey($offset);
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\IDictionary::remove()
     */
    public function remove($key) {
        return $this->removeKey($key);
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\IDictionary::removeKey()
     */
    public function removeKey($key) {
        $i = $this->indexOfByOffset($key);
        if (false !== $i) {
            array_splice($this->_items, $i, 1);
            return true;
        }
        
        return false;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::rewind()
     */
    public function rewind() {
        $this->_iterator = new \ArrayIterator($this->_items);
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::toEnumerable()
     */
    protected static function toEnumerable($input) {
    	if ($input instanceof IEnumerable) {
    		return $input;
    	}
    	
    	if (is_array($input)) {
    		$input = new \ArrayIterator($input);
    	}
    	
        return new Enumerable($input);
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::valid()
     */
    public function valid() {
        return $this->_iterator->valid();
    }

    /**
     * (non-PHPdoc)
     * @see \System\Collections\IDictionary::values()
     */
    public function values() {
        return static::toEnumerable($this->valuesInner());
    }
        
    private function valuesInner() {
    	$result = array();
    	
        foreach ($this->_items as $i) {
            $result[] = $i->value;
        }
        
        return $result;
    }
}
