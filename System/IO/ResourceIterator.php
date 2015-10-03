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

namespace System\IO;

use \System\ArgumentException;
use \System\ArgumentOutOfRangeException;
use \System\DisposableBase;
use \System\ClrString;


/**
 * Buffered iteration over a resource.
 *
 * @package System\IO
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class ResourceIterator extends DisposableBase implements \SeekableIterator {
    /**
     * @var string
     */
    private $_buffer = null;
    /**
     * @var int
     */
    private $_bufferSize;
    /**
     * @var int|null
     */
    private $_initialKey;
    /**
     * @var int
     */
    private $_key;
    /**
     * @var resource
     */
    private $_resource;


    /**
     * Initializes a new instance of that class.
     *
     * @param resource $resource The underlying resource.
     * @param int $bufferSize The buffer size (in bytes) to use.
     *
     * @throws ArgumentException $resource is no valid resource.
     * @throws ArgumentOutOfRangeException $bufferSize is less than 1.
     */
    public function __construct($resource, int $bufferSize = 1) {
        if (!\is_resource($resource)) {
            throw new ArgumentException('resource');
        }

        if ($bufferSize < 1) {
            throw new ArgumentOutOfRangeException('bufferSize');
        }

        $this->_resource = $resource;
        $this->_bufferSize = $bufferSize;

        $this->next();

        if (false !== $this->_key) {
            $this->_initialKey = $this->_key;
        }
    }

    /**
     * {@inheritDoc}
     */
    public final function current() {
        $this->throwIfDisposed();

        return $this->_buffer;
    }

    /**
     * Creates a new instance of that class from a file path.
     *
     * @param string $path The path of the file to open.
     * @param int $bufferSize The buffer size to use.
     * @param string $mode The mode to open the file with.
     *
     * @return static
     *
     * @throws ArgumentOutOfRangeException $bufferSize is less than 1.
     * @throws FileNotFoundException File was not found.
     * @throws IOException File could not be open.
     */
    public static function forFile($path, int $bufferSize = 1024, $mode = 'r') {
        if ($bufferSize < 1) {
            throw new ArgumentOutOfRangeException('bufferSize');
        }

        $path = ClrString::valueToString($path);

        $fullPath = \realpath($path);
        if (false === $fullPath) {
            throw new FileNotFoundException($path);
        }

        $res = \fopen($fullPath, ClrString::valueToString($mode, false));
        if (!\is_resource($res)) {
            throw new IOException(ClrString::format("File '{0}' could not be opened!",
                                                    $fullPath));
        }

        return new static($res, $bufferSize);
    }

    /**
     * {@inheritDoc}
     */
    public final function key() {
        $this->throwIfDisposed();

        return $this->_key;
    }

    /**
     * Tries to load the next data into internal buffer.
     *
     * @return bool Operation was successful or not.
     */
    protected final function loadNext() : bool {
        $this->throwIfDisposed();

        $this->_buffer = \fread($this->_resource, $this->_bufferSize);

        return false !== $this->_buffer;
    }

    /**
     * {@inheritDoc}
     */
    public final function next() {
        $this->throwIfDisposed();

        $this->updateKey();
        $this->loadNext();
    }

    /**
     * {@inheritDoc}
     */
    protected final function onDispose(bool $disposing, bool &$isDisposed = false) {
        if (!$disposing) {
            return;
        }

        if (!\fclose($this->_resource)) {
            $isDisposed = false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public final function rewind() {
        $this->throwIfDisposed();

        if (null !== $this->_initialKey) {
            $invokeNext = 0 === \fseek($this->_resource, $this->_initialKey);
        }
        else {
            $invokeNext = \rewind($this->_resource);
        }

        if ($invokeNext) {
            $this->next();
        }
    }

    /**
     * {@inheritDoc}
     */
    public final function seek($offset) {
        $this->throwIfDisposed();

        if (0 === \fseek($this->_resource, $offset)) {
            $this->next();
        }
    }

    /**
     * Tries to update the key.
     *
     * @return bool Operation was successful or not.
     */
    protected final function updateKey() : bool {
        $this->throwIfDisposed();

        $this->_key = \ftell($this->_resource);

        return false !== $this->_key;
    }

    /**
     * {@inheritDoc}
     */
    public final function valid() {
        $this->throwIfDisposed();

        return ('' !== $this->_buffer) &&
               (false !== $this->_buffer);
    }
}
