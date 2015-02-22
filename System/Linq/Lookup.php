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


namespace System\Linq;

use \System\Collections\IDictionary;
use \System\Collections\Generic\EnumerableBase;
use \System\Collections\Generic\IEnumerable;
use \System\Collections\Dictionary;


/**
 * A lookup object.
 * 
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
final class Lookup extends EnumerableBase implements ILookup {
    /**
     * @var IDictionary
     */
    private $_dict;
    
    
    /**
     * Initializes a new instance of that class.
     * 
     * @param IEnumerable $grps The sequence of groupings.
     */
    public function __construct($grps) {
        if ($grps instanceof IDictionary) {
            $this->_dict = $grps;
        }
        else {
            $newDict = new Dictionary();
            foreach ($grps as $g) {
                $newDict->add($g->key(),
                              $g);
            }
            
            $this->_dict = $newDict;
        }
    }
    
    
    /**
     * (non-PHPdoc)
     * @see \System\Linq\ILookup::containsKey()
     */
    public function containsKey($key) {
        return $this->offsetExists($key);
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::count()
     */
    public function count() {
        return count($this->_dict);
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::current()
     */
    public function current() {
        return $this->_dict
                    ->current()
                    ->value();
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::key()
     */
    public function key() {
        return $this->_dict
                    ->key();
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::next()
     */
    public function next() {
        return $this->_dict
                    ->next();
    }
    
    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($key) {
        return isset($this->_dict[$key]);
    }
    
    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($key) {
        $result = null;
        if (isset($this->_dict[$key])) {
            $result = $this->_dict[$key]
                           ->getIterator();
        }
                
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($key, $value) {
        $this->_dict[$key] = $value;
    }
    
    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($key) {
        unset($this->_dict[$key]);
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::rewind()
     */
    public function rewind() {
        $this->_dict
             ->rewind();
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::valid()
     */
    public function valid() {
        return $this->_dict
                    ->valid();
    }
}
