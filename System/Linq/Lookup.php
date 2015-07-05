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


use \System\Collections\Dictionary;
use \System\Collections\EnumerableBase;
use \System\Collections\IDictionary;
use \System\Collections\IEnumerable;


/**
 * A lookup object.
 *
 * @package System\Linq
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
            $grps = static::asIterator($grps, true);

            $this->_dict = new Dictionary();
            while ($grps->valid()) {
                $g = $grps->current();

                $this->_dict
                     ->add($g->key(), $g);

                $grps->next();
            }
        }
    }


    public function containsKey($key) {
        return $this->offsetExists($key);
    }

    public function count() {
        return \count($this->_dict);
    }

    public function current() {
        return $this->_dict->current()
                           ->value();
    }

    public function key() {
        return $this->_dict->key();
    }

    public function next() {
        return $this->_dict->next();
    }

    public function offsetExists($key) {
        return isset($this->_dict[$key]);
    }

    public function offsetGet($key) {
        if (isset($this->_dict[$key])) {
            return $this->_dict[$key]
                        ->getIterator();
        }

        $this->throwException('Key not found!');
    }

    public function offsetSet($key, $value) {
        $this->_dict[$key] = $value;
    }

    public function offsetUnset($key) {
        unset($this->_dict[$key]);
    }

    public function reset() {
        $this->_dict->reset();
        return $this;
    }

    public function valid() {
        return $this->_dict->valid();
    }
}
