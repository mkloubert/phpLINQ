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
use \System\ILazy;
use \System\IString;
use \System\Lazy;


/**
 * An object that provides information about a file.
 *
 * @package System\IO
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class FileInfo extends FileSystemInfo implements IFileInfo {
    /**
     * @var ILazy
     */
    private $_directory;
    /**
     * @var bool
     */
    private $_exists;
    /**
     * @var IString
     */
    private $_fullName;
    /**
     * @var IString
     */
    private $_name;


    /**
     * Initializes a new instance of that class.
     *
     * @param string $path The path of the directory.
     */
    public function __construct($path) {
        $path = static::normalizePath($path);

        $this->_fullName = new ClrString($path);
        $this->_name     = new ClrString(\basename($path));

        $this->refresh();
    }


    /**
     * {@inheritDoc}
     */
    public final function directory() {
        return $this->_directory
                    ->value();
    }

    /**
     * {@inheritDoc}
     */
    public final function directoryName() {
        return null !== $this->directory() ? $this->directory()->fullName()
                                           : null;
    }

    /**
     * {@inheritDoc}
     */
    public final function exists() : bool {
        return $this->_exists;
    }

    /**
     * {@inheritDoc}
     */
    public final function fullName() {
        return $this->_fullName;
    }

    /**
     * {@inheritDoc}
     */
    public final function name() {
        return $this->_name;
    }

    /**
     * {@inheritDoc}
     */
    public function refresh() {
        // reset first
        $this->_directory = null;
        $this->_exists    = false;

        $me = $this;

        $path = (string)$this->_fullName;
        if (@\is_link($path)) {
            $link = @\readlink($path);
            if (false !== $link) {
                $path = $link;
            }
        }

        // parent directory
        $this->_exists = \file_exists($path) &&
                         \is_file($path);

        // parent directory
        $this->_directory = new Lazy(function() use ($me) {
            $result = null;

            $dirPath = \dirname((string)$me->fullName());

            if (!ClrString::isNullOrWhitespace($dirPath)) {
                $result = new DirectoryInfo($dirPath);
            }

            return $result;
        });
    }
}
