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

namespace System;

use \System\Linq\Enumerable;


/**
 * A constant string.
 *
 * @package System
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class ClrString extends Enumerable implements IString {
    /**
     * @var string
     */
    protected $_wrappedValue;


    /**
     * Initializes a new instance of that class.
     *
     * @param mixed $value The value to wrap (as string).
     */
    public function __construct($value) {
        $this->_wrappedValue = static::valueToString($value);

        parent::__construct($this->createStringIterator());
    }


    /**
     * Creates an iterator for the current string value.
     *
     * @return \Iterator The created iterator.
     */
    protected function createStringIterator() : \Iterator {
        return new \ArrayIterator(\str_split($this->_wrappedValue));
    }

    /**
     * {@inheritDoc}
     */
    public final function getWrappedValue() : string {
        return $this->_wrappedValue;
    }

    /**
     * {@inheritDoc}
     */
    public function toString() : IString {
        return new static($this->_wrappedValue);
    }

    /**
     * Updates the inner iterator.
     */
    protected final function updateStringIterator() {
        $this->_i = $this->createStringIterator();
    }

    /**
     * Converts a value to a PHP string.
     *
     * @param mixed $value The input value.
     * @param bool $nullAsEmpty Handle (null) as empty or not.
     *
     * @return string $value as string.
     */
    public static function valueToString($value, bool $nullAsEmpty = true) : string {
        if (\is_string($value)) {
            return $value;
        }

        if (null === $value) {
            return $nullAsEmpty ? '' : null;
        }

        return \strval($value);
    }
}
