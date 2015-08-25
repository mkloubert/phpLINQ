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
 * An exception.
 *
 * @package System
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class Exception extends \Exception implements IException {
    /**
     * Initializes a new instance of that class.
     *
     * @param string $message The message.
     * @param int $code The code.
     * @param \Exception $innerException The inner exception.
     */
    public function __construct($message = null,
                                \Exception $innerException = null,
                                $code = 0) {

        parent::__construct($message, $code, $innerException);
    }

    /**
     * Object::toString()
     */
    public final function __toString() {
        return $this->toString();
    }


    /**
     * {@inheritDoc}
     */
    public function equals($other) {
        return $this == $other;
    }

    /**
     * {@inheritDoc}
     */
    public function toString() {
        return parent::__toString();
    }
}
