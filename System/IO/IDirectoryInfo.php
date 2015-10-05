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
use \System\IString;
use \System\Collections\IEnumerable;


/**
 * Describes an object that provides information about a directory.
 *
 * @package System\IO
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
interface IDirectoryInfo extends IFileSystemInfo {
    /**
     * Gets the directory where the directory is stored.
     *
     * @return IDirectoryInfo The directory.
     */
    function directory();

    /**
     * Gets the full name of the directory where the file is stored.
     *
     * @return IString The directory name.
     */
    function directoryName();

    /**
     * Returns a sequence of directory information of that directory.
     *
     * @return IEnumerable The list of directories.
     */
    function enumerateDirectories() : IEnumerable;

    /**
     * Returns a sequence of file information of that directory.
     *
     * @return IEnumerable The list of files.
     */
    function enumerateFiles() : IEnumerable;

    /**
     * Returns an array of the directories of the directory.
     *
     * @param callable $predicate The custom filter to use.
     *
     * @return IDirectoryInfo[] The list of files.
     *
     * @throws ArgumentException $predicate is no valid callable / lambda expression.
     */
    function getDirectories($predicate = null) : array;

    /**
     * Returns an array of the files of the directory.
     *
     * @param callable $predicate The custom filter to use.
     *
     * @return IFileInfo[] The list of files.
     *
     * @throws ArgumentException $predicate is no valid callable / lambda expression.
     */
    function getFiles($predicate = null) : array;
}
