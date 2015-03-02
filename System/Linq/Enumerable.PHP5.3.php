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

use \System\Collections\Generic\EnumerableBase as EnumerableBase;
use \System\Collections\Generic\IEnumerable;


/**
 * A simple sequence based on an iterator (PHP 5.3).
 * 
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class Enumerable extends EnumerableBase {
    /**
     * @var \Iterator
     */
    private $_i;
    
    
    /**
     * Initializes a new instance of that class.
     * 
     * @param \Iterator $i The inner iterator.
     */
    public function __construct(\Iterator $i) {
        $this->_i = $i;
    }
    
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::count()
     */
    public function count() {
        if ($this->_i instanceof \Countable) {
            return $this->_i->count();
        }
    
        return parent::count();
    }
    
    /**
     * Creates a new empty instance.
     * 
     * @return \System\Linq\Enumerable The new instance.
     */
    public static function createEmpty() {
        return static::fromArray(array());
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::current()
     */
    public function current() {
        return $this->_i->current();
    }
    
    /**
     * Creates a new instance from an array.
     * 
     * @param array $array The array to use.
     * 
     * @return \System\Linq\Enumerable The new instance.
     */
    public static function fromArray(array $array) {
        return new static(new \ArrayIterator($array));
    }
    
    /**
     * Creates a new instance from a list of values.
     * 
     * @return \System\Linq\Enumerable The new instance.
     */
    public static function fromValues() {
        return static::fromArray(func_get_args());
    }

    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::key()
     */
    public function key() {
        return $this->_i->key();
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::next()
     */
    public function next() {
        $this->_i->next();
    }
    
    /**
     * Creates a sequence of numbers.
     *
     * @param number $start The start value.
     * @param number $count The number of values.
     * @param number|callable The value for increasing each value or
     *                        a function that provides that value.
     *
     * @return \System\Linq\Enumerable The new instance.
     */
    public final static function range($start, $count, $increaseBy = 1) {
        $increaseFunc = $increaseBy;
        if (!is_callable($increaseFunc)) {
            $increaseFunc = function($value, $index) use ($increaseBy) {
                return $increaseBy;
            };
        }
        
        return static::toEnumerable(static::rangeInner($start, $count,
                                                       $increaseFunc));
    }
    
    private static function rangeInner($start, $count, $increaseFunc) {
    	$result = array();
    	
        $index = -1;
        
        $i = $start;
        while (($count - ++$index) > 0) {
            $result[] = $i;
            
            $i += $increaseFunc($i, $index);
        }
        
        return $result;
    }
    
    /**
     * Creates a sequence of items.
     * 
     * @param mixed $val The value / object to repeat.
     * @param integer $count The number of items.
     * 
     * @return \System\Linq\Enumerable The new insatnce.
     */
    public final static function repeat($val, $count) {
        return static::toEnumerable(static::repeatInner($val, $count));
    }
    
    private static function repeatInner($val, $count) {
    	$result = array();
    	
        while ($count > 0) {
            $result[] = $val;
            --$count;
        }
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::rewind()
     */
    public function rewind() {
        return $this->_i->rewind();
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
        
        return new static($input);
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\EnumerableBase::valid()
     */
    public function valid() {
        return $this->_i->valid();
    }
}
