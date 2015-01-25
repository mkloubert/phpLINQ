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


namespace System\Collections\Generic;


/**
 * A general exception for sequences.
 * 
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class EnumerableException extends \Exception {
    /**
     * @var IEnumerable
     */
    private $_sequence;
    
    
    /**
     * Initializes a new instance of that class.
     * 
     * @param IEnumerable $seq The underlying sequence.
     * @param string $message The message.
     * @param number $code The code.
     * @param string $previous The inner/previous exception.
     */
    public function __construct(IEnumerable $seq,
                                $message = null,
                                $code = 0,
                                $previous = null) {
        parent::__construct($message, $code, $previous);
        
        $this->_sequence = $seq;
    }
    
    
    /**
     * Gets the underlying sequence.
     * 
     * @return \System\Collections\Generic\IEnumerable The underlying sequence.
     */
    public function getSequence() {
        return $this->_sequence;
    }
}
