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
 * A general exception for sequences.
 *
 * @package System\Collections
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class EnumerableException extends \System\Exception {
    /**
     * @var IEnumerable
     */
    protected $_sequence;


    /**
     * Initializes a new instance of that class.
     *
     * @param IEnumerable $seq The underlying sequence.
     * @param string $message The message.
     * @param \Exception $innerException The inner exception.
     * @param int $code The code.
     */
    public function __construct(IEnumerable $seq,
                                $message = null,
                                \Exception $innerException = null,
                                $code = 0) {

        $this->_sequence = $seq;

        parent::__construct($message, $innerException, $code);
    }


    /**
     * Gets the underlying sequence.
     *
     * @return IEnumerable The underlying sequence.
     */
    public function getSequence() {
        return $this->_sequence;
    }
}
