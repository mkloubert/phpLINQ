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

use \System\ClrString;
use \System\IString;


/**
 * A file stream.
 *
 * @package System\IO
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class FileStream extends Stream {
    /**
     * @var IString
     */
    private $_file;


    /**
     * Initializes a new instance of that class.
     *
     * @param string $path The path of the file.
     * @param string $mode The mode
     *
     * @throws IOException File could not be opened.
     */
    public function __construct($path, $mode = 'r+') {
        $path = ClrString::asString($path);

        $res = \fopen((string)$path, ClrString::valueToString($mode, false));
        if (false === $res) {
            $this->throwIOException('Could not open file!');
        }

        $this->_file = $path;

        parent::__construct($res, true);
    }


    /**
     * Gets the path of the underlying file.
     *
     * @return IString The file path.
     */
    public final function file() : IString {
        return $this->_file;
    }

    /**
     * {@inheritDoc}
     */
    protected function lengthInner() : int {
        $result = \filesize($this->_file);
        if (false === $result) {
            $this->throwIOException('Could not determine length!');
        }

        return $result;
    }

    /**
     * Opens a file for reading only (mode 'r').
     *
     * @param string $file The path of the file.
     *
     * @return static
     */
    public static function openRead($file) {
        return new static($file, 'r');
    }

    /**
     * Opens a file for writing only (mode 'c').
     *
     * @param string $file The path of the file.
     *
     * @return static
     */
    public static function openWrite($file) {
        return new static($file, 'c');
    }
}
