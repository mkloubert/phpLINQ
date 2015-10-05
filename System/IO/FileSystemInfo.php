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
use \System\Object;


/**
 * A base object that provides information about a file system object.
 *
 * @package System\IO
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
abstract class FileSystemInfo extends Object implements IFileSystemInfo {
    /**
     * {@inheritDoc}
     */
    abstract function exists() : bool;

    /**
     * {@inheritDoc}
     */
    abstract public function fullName();

    /**
     * {@inheritDoc}
     */
    abstract public function name();

    /**
     * Checks if a path is rooted or not.
     *
     * @param string $path The value to check.
     *
     * @return bool Is rooted or not.
     */
    protected static function isPathRooted($path) {
        $path = \trim(ClrString::valueToString($path));

        while (false !== \stripos($path, "\\")) {
            $path = \str_ireplace("\\", '/', $path);
        }

        if ((\strlen($path) >= 1) && ('/' === $path[0])) {
            return true;
        }

        if (false !== \stripos($path, ':')) {
            return true;
        }

        return false;
    }

    /**
     * Normalizes a path.
     *
     * @param string $path The input value.
     *
     * @return string The normalized value.
     */
    protected static function normalizePath($path) {
        $path = ClrString::valueToString($path);

        if (!static::isPathRooted($path)) {
            $path = \getcwd() . \DIRECTORY_SEPARATOR . $path;
        }

        $path = \str_ireplace(\DIRECTORY_SEPARATOR, '/', $path);

        $segments = \explode('/', \trim($path, '/'));
        $result = [];
        foreach($segments as $segment){
            if (('.' === $segment) || ('' === $segment)) {
                continue;
            }

            if ('..' === $segment) {
                \array_pop($result);
            }
            else {
                \array_push($result, $segment);
            }
        }

        return \str_ireplace('/', \DIRECTORY_SEPARATOR,
                             \implode('/', $result));
    }

    /**
     * {@inheritDoc}
     */
    abstract public function refresh();

    /**
     * {@inheritDoc}
     */
    public final function toString() : IString {
        return new ClrString($this->fullName());
    }
}
