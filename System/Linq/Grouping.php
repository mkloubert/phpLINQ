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

use \System\Collections\Generic\IEnumerable as IEnumerable;


/**
 * A simple grouping of elements.
 * 
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
final class Grouping implements IGrouping {
    private $_items;
    private $_key;
    
    
    /**
     * Initializes a new instance of that class.
     * 
     * @param mixed $key The key.
     * @param IEnumerable $items The sequence with the items.
     */
    public function __construct($key, IEnumerable $items) {
        $this->_key = $key;
        $this->_items = $items;
    }
    
    
    /**
     * (non-PHPdoc)
     * @see Countable::count()
     */
    public function count() {
        return $this->getIterator()->count();
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IGrouping::getIterator()
     */
    public function getIterator() {
        return $this->_items;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IGrouping::key()
     */
    public function key() {
        return $this->_key;
    }
}
