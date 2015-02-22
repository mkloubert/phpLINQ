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

use \System\Collections\Generic\EnumerableBase;


/**
 * A common collection / list.
 * 
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
final class Collection extends EnumerableBase implements IList {
    private $_comparer;
    private $_items;
    private $_iterator;
    
    
    /**
     * Initializes a new instance of that class.
     * 
     * @param Traversable|array $items The initial items.
     */
    public function __construct($items = null, $comparer = null) {
        $this->checkForFunctionOrThrow($comparer, 2);
        
        $this->_comparer = static::getComparerSafe($comparer);
        
        $this->clear();
        if (!is_null($items)) {
            $this->addRange($items);
        }
        
        $this->reset();
    }
    
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\IList::add()
     */
    public function add($item) {
        $this->_items[] = $item;
        
        return $this->count() - 1;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\IList::addItems()
     */
    public function addItems() {
    	$this->addRange(func_get_args());
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\IList::addRange()
     */
    public function addRange($items) {
    	foreach ($items as $i) {
    		$this->_items[] = $i;
    	}
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\IList::clear()
     */
    public function clear() {
        $this->_items = array();
    }
    
    private function compareItems($x, $y) {
        $c = $this->_comparer;
        
        return $c($x, $y);
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\IList::containsItem()
     */
    public function containsItem($item) {
        return $this->indexOf($item) > -1;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::count()
     */
    public final function count() {
        return count($this->_items);
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::current()
     */
    public function current() {
        return $this->_iterator->current();
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::elementAtOrDefault()
     */
    public final function elementAtOrDefault($index, $defValue = null) {
        if (isset($this->_items[$index])) {
            return $this->_items[$index];
        }
        
        return $defValue;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\IList::indexOf()
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
     * (non-PHPdoc)
     * @see \System\Collections\IList::insert()
     */
    public function insert($index, $item) {
        if (!$this->offsetExists($index)) {
            $this->throwIndexOfOfRange($index);
        }
        
        $newItems = array();
        for ($i = 0; $i < count($this->_items); $i++) {
            if ($i == $index) {
                $newItems[] = $item;
            }
            
            $newItems[] = $this->_items[$i];
        }
        
        $this->_items = $newItems;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\IList::isFixedSize()
     */
    public function isFixedSize() {
        return false;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\IList::isReadOnly()
     */
    public function isReadOnly() {
        return false;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\IList::isSynchronized()
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
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($index) {
        return isset($this->_items[$index]);
    }
    
    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($index) {
        if ($this->offsetExists($index)) {
            return $this->_items[$index];
        }

        $this->throwIndexOfOfRange($index);
    }
    
    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($index, $value) {
        if (is_null($index)) {
            $this->add($value);
            return;
        }
        
        if ($this->offsetExists($index)) {
            $this->_items[$index] = $value;
            return;
        }
        
        $this->throwIndexOfOfRange($index);
    }
    
    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($index) {
        $this->removeAt($index);
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
     * @see \System\Collections\IList::remove()
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
     * (non-PHPdoc)
     * @see \System\Collections\IList::removeAt()
     */
    public function removeAt($index) {
        if ($this->offsetExists($index)) {
            array_splice($this->_items, $index, 1);
            return;
        }
        
        $this->throwIndexOfOfRange($index);
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::rewind()
     */
    public function rewind() {
        $this->_iterator = new \ArrayIterator($this->_items);
    }
    
    private function throwIndexOfOfRange($index) {
        $this->throwException(sprintf("Index '%s' not found!",
                                      $index));
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::valid()
     */
    public function valid() {
        return $this->_iterator->valid();
    }
}
