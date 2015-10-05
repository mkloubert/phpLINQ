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

use \System\IObject;
use \System\IString;


/**
 * Describes an object that provides information about a file system object.
 *
 * @package System\IO
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
interface IFileSystemInfo extends IObject {
    /**
     * Checks if the object exists.
     *
     * @return bool Exists or not.
     */
    function exists() : bool;

    /**
     * Gets the full name.
     *
     * @return IString The full name.
     */
    function fullName();

    /**
     * Gets the name.
     *
     * @return IString The name.
     */
    function name();

    /**
     * Refreshes the state of the object.
     */
    function refresh();
}
