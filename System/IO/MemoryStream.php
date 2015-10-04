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
 * A stream that stores the data in the memory.
 *
 * @package System\IO
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class MemoryStream extends Stream {
    /**
     * @var string
     */
    private $_buffer;
    /**
     * @var int
     */
    private $_position = 0;
    /**
     * @var bool
     */
    private $_writable;


    /**
     * Initializes a new instance of that class.
     *
     * @param string $buffer The initial data.
     * @param bool $writable New instance is writable or read only.
     */
    public function __construct($buffer = null, bool $writable = true) {
        $this->_buffer = ClrString::valueToString($buffer);
        $this->_writable = $writable;
    }


    /**
     * {@inheritDoc}
     */
    public final function canRead() : bool {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public final function canSeek() : bool {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public final function canWrite() : bool {
        return $this->_writable;
    }

    /**
     * {@inheritDoc}
     */
    protected function flushInner() {
        // nothing to do here
    }

    /**
     * {@inheritDoc}
     */
    protected final function lengthInner() : int {
        return \strlen($this->_buffer);
    }

    /**
     * Normalizes the value for MemoryStream::position()
     */
    protected final function normalizePosition() {
        if ($this->_position < 0) {
            $this->_position = 0;
        }

        if ($this->_position >= ($this->length() + 1)) {
            $this->_position = $this->length();
        }
    }

    /**
     * {@inheritDoc}
     */
    protected final function onDispose(bool $disposing, bool &$isDisposed) {
        if (!$disposing) {
            return;
        }

        $this->_buffer = null;
    }

    /**
     * {@inheritDoc}
     */
    protected final function positionInner() : int {
        return $this->_position;
    }

    /**
     * {@inheritDoc}
     */
    protected function readInner(int $count) {
        if (($this->position() + $count - 1) >= $this->length()) {
            $count = $this->length() - $this->position();
        }

        $result = \substr($this->_buffer, $this->position(), $count);

        $this->_position += $count;
        $this->normalizePosition();

        return $result;
    }

    /**
     * @see Stream::seek()
     */
    protected function seekInner(int $offset, int $where) : bool {
        switch ($where) {
            case \SEEK_SET:
                $newPos = $offset;
                break;

            case \SEEK_CUR:
                $newPos = $this->position() + $offset;
                break;

            case \SEEK_END:
                $newPos = $this->length() + $offset;
                break;
        }

        $this->_position = $newPos;
        $this->normalizePosition();

        return true;
    }

    /**
     * {@inheritDoc}
     */
    protected function setLengthInner(int $value) {
        if ($value < 0) {
            $value = 0;
        }

        if ($value !== $this->length()) {
            if ($value < $this->length()) {
                $this->_buffer = \substr($this->_buffer, 0, $value);
            }
            else {
                $this->_buffer .= \str_repeat("\0", $value - $this->length());
            }
        }

        $this->normalizePosition();
    }

    /**
     * {@inheritDoc}
     */
    public final function toString() : IString {
        return new ClrString($this->_buffer);
    }

    /**
     * {@inheritDoc}
     */
    protected function writeInner(string $data) : int {
        $result = \strlen($data);

        for ($i = 0; $i < $result; $i++) {
            $char = $data[$i];

            if ($this->position() >= $this->length()) {
                $this->_buffer .= $char;
            }
            else {
                $this->_buffer[$this->position() + $i] = $char;
            }

            ++$this->_position;
            $this->normalizePosition();
        }

        return $result;
    }
}
