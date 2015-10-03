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

namespace System\IO;


/**
 * Indicates that a stream has been closed.
 *
 * @package System\IO
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class StreamClosedException extends IOException {
    /**
     * @var IStream
     */
    private $_stream;


    /**
     * Initializes a new instance of that class.
     *
     * @param IStream $stream The underlying stream.
     * @param string $message The message.
     * @param \Exception $innerException The inner exception.
     * @param int $code The code.
     */
    public function __construct(IStream $stream = null,
                                $message = null,
                                \Exception $innerException = null,
                                int $code = 0) {

        $this->_stream = $stream;

        parent::__construct($message, $innerException, $code);
    }


    /**
     * Gets the underlying stream.
     *
     * @return IStream The underlying stream.
     */
    public function stream() {
        return $this->_stream;
    }
}
