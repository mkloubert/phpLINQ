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
 * Stores data for a dictionary entry.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 * @package System\Collections
 */
final class DictionaryEntry extends \System\Object implements \Serializable {
    const ARRAY_KEY_KEY   = 'key';
    const ARRAY_KEY_VALUE = 'value';


    private $_key;
    private $_value;


    /**
     * Initializes a new instance of that class.
     *
     * @param mixed $key The key.
     * @param mixed $value The value.
     */
    public function __construct($key, $value) {
        $this->_key   = $key;
        $this->_value = $value;
    }


    /**
     * Gets the key.
     *
     * @return mixed The key.
     */
    public function key() {
        return $this->_key;
    }

    public function serialize() {
        return \json_encode(array(self::ARRAY_KEY_KEY   => $this->key(),
                                  self::ARRAY_KEY_VALUE => $this->value()));
    }

    /**
     * Gets the value.
     *
     * @return mixed The value.
     */
    public function value() {
        return $this->_value;
    }

    public function unserialize($serialized) {
        $arr = \json_decode($serialized, true);

        $this->__construct($arr[self::ARRAY_KEY_KEY],
                           $arr[self::ARRAY_KEY_VALUE]);
        unset($arr);
    }
}
