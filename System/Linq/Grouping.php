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


/**
 * A simple grouped iterator.
 *
 * @package System\Linq
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
final class Grouping implements IGrouping {
    private $_iterator;
    private $_key;


    /**
     * Initializes a new instance of that class.
     *
     * @param mixed $key The key.
     * @param \Iterator $iterator The underlying iterator.
     */
    public function __construct($key, \Iterator $iterator) {
        $this->_key      = $key;
        $this->_iterator = $iterator;
    }


    public final function getIterator() {
        return $this->_iterator;
    }

    public function key() {
        return $this->_key;
    }
}
