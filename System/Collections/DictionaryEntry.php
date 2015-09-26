<?php

/**********************************************************************************************************************
 * phpLINQ (https://github.com/mkloubert/phpLINQ)                                                                     *
 *                                                                                                                    *
 * Copyright (c) 2015, Marcel Joachim Kloubert <marcel.kloubert@gmx.net>                                              *
 * All rights reserved.                                                                                               *
 *                                                                                                                    *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the   *
 * following conditions are met:                                                                                      *
 *                                                                                                                    *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the          *
 *    following disclaimer.                                                                                           *
 *                                                                                                                    *
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the       *
 *    following disclaimer in the documentation and/or other materials provided with the distribution.                *
 *                                                                                                                    *
 * 3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote    *
 *    products derived from this software without specific prior written permission.                                  *
 *                                                                                                                    *
 *                                                                                                                    *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, *
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE  *
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, *
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR    *
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,  *
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE   *
 * USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.                                           *
 *                                                                                                                    *
 **********************************************************************************************************************/

namespace System\Collections;


/**
 * An entry of a dictionary.
 *
 * @package System\Collections
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class DictionaryEntry extends \System\Object implements IDictionaryEntry {
    /**
     * Name of the array key for the key value of a serialized entry.
     */
    const ARRAY_KEY_KEY   = 'key';
    /**
     * Name of the array key for the value of a serialized entry.
     */
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
     * {@inheritDoc}
     */
    public final function key() {
        return $this->_key;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize() {
        return \serialize([static::ARRAY_KEY_KEY   => $this->key(),
                           static::ARRAY_KEY_VALUE => $this->value()]);
    }

    /**
     * {@inheritDoc}
     */
    public final function value() {
        return $this->_value;
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized) {
        $arr = \unserialize($serialized) ?? [];

        $this->__construct($arr[static::ARRAY_KEY_KEY],
                           $arr[static::ARRAY_KEY_VALUE]);
    }
}
