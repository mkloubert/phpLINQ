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
 * Is thrown if an argument is invalid.
 *
 * @package System
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class ArgumentException extends Exception {
    /**
     * @var string
     */
    protected $_paramName;


    /**
     * Initializes a new instance of that class.
     *
     * @param string $paramName The name of the underlying parameter.
     * @param string $message The message.
     * @param int $code The code.
     * @param \Exception $innerException The inner exception.
     */
    public function __construct($paramName = null,
                                $message = null, \Exception $innerException = null, $code = 0) {

        $this->_paramName = $paramName;

        parent::__construct($message, $innerException, $code);
    }


    /**
     * Gets the name of the underlying parameter.
     *
     * @return string The name of the underlying parameter.
     */
    public function getParamName() {
        return $this->_paramName;
    }
}
