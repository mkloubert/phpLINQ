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


namespace System;


/**
 * Wraps an object or value.
 *
 * @package System
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class ObjectWrapper extends Object {
    /**
     * @var mixed
     */
    protected $_wrappedValue;


    /**
     * Initializes a new instance of that class.
     *
     * @param mixed $wrappedValue The wrapped value.
     */
    public function __construct($wrappedValue) {
        $this->_wrappedValue = $wrappedValue;
    }


    public function equals($other) {
        return $this->getWrappedValue() == $other;
    }

    /**
     * Returns the wrapped value.
     *
     * @return mixed The wrapped value.
     */
    public final function getWrappedValue() {
        return $this->_wrappedValue;
    }

    public final function toString() {
        return \strval($this->getWrappedValue());
    }
}
