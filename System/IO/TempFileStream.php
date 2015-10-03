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

use \System\ArgumentException;
use \System\ClrString;


/**
 * A stream for handling a temp file.
 *
 * @package System\IO
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class TempFileStream extends FileStream {
    /**
     * @var bool
     */
    private $_deleteOnDispose;


    /**
     * Initializes a new instance of that class.
     *
     * @param string $prefix The file prefix.
     * @param callable $fileFactory The custom file factory to use.
     * @param bool $deleteOnDispose Delete the file when TempFileStream::dispose() method is called or not.
     *
     * @throws ArgumentException $fileFactory is no valid callable / lambda expression.
     * @throws IOException File could not be opened.
     */
    public function __construct($prefix = null, $fileFactory = null, bool $deleteOnDispose = true) {
        $prefix = ClrString::valueToString($prefix);

        $fileFactory = static::asCallable($fileFactory);
        if (null === $fileFactory) {
            $fileFactory = function($prefix, $sysTempDir) : string {
                return \tempnam($sysTempDir, $prefix);
            };
        }

        $this->_deleteOnDispose = $deleteOnDispose;

        parent::__construct($fileFactory($prefix, \sys_get_temp_dir()),
                            'c+');
    }


    /**
     * {@inheritDoc}
     */
    protected function onDispose(bool $disposing, bool &$isDisposed) {
        parent::onDispose($disposing, $isDisposed);

        if (!$disposing || !$isDisposed) {
            return;
        }

        if ($this->_deleteOnDispose) {
            $isDisposed = \unlink($this->file());
        }
    }
}
