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


namespace System\Collections\Generic;

/**
 * A common set.
 * 
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
final class Set extends EnumerableBase implements ISet {
    private $_comparer;
    private $_items;
    private $_iterator;
    
    
    /**
     * Initializes a new instance of that class.
     * 
     * @param callable $comparer The custom item comparer to use.
     */
    public function __construct($comparer = null) {
        $this->checkForFunctionOrThrow($comparer, 2);
        
        $this->_comparer = static::getComparerSafe($comparer);
        
        $this->clear();
        
        $this->reset();
    }
    
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\ISet::add()
     */
    public function add($item) {
        if (!$this->containsItem($item)) {
            $this->_items[] = $item;
            return true;
        }
        
        return false;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\ISet::clear()
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
     * @see \System\Collections\Generic\ISet::containsItem()
     */
    public function containsItem($item) {
        foreach ($this->_items as $i) {
            if ($this->compareItems($item, $i)) {
                // found
                return true;
            }
        }
        
        // not found
        return false;
    }

    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::count()
     */
    public function count() {
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
     * @see \System\Collections\Generic\ISet::isReadOnly()
     */
    public function isReadOnly() {
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
     * @see \System\Collections\Generic\ISet::remove()
     */
    public function remove($item) {
        foreach ($this->_items as $index => $value) {
            if ($this->compareItems($item, $value)) {
                // found
                
                array_splice($this->_items, $index, 1);
                return true;
            }
        }
        
        // not found
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
     * @see \System\Collections\Generic\EnumerableBase::next()
     */
    public function next() {
        $this->_iterator->next();
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::valid()
     */
    public function valid() {
        return $this->_iterator->valid();
    }
}
