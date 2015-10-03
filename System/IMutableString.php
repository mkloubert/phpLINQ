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

namespace System;


/**
 * Describes a mutable string.
 *
 * @package System
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
interface IMutableString extends IString {
    /**
     * Appends a value.
     *
     * @param mixed ...$value One or more value to append.
     *
     * @return IMutableString That instance.
     */
    function append($value) : IMutableString;

    /**
     * Appends a list of values.
     *
     * @param mixed ...$values One or more value lists to append.
     *
     * @return IMutableString That instance.
     */
    function appendArray($values = null) : IMutableString;

    /**
     * Appends a formatted string.
     *
     * @param string $format The format string.
     * @param mixed ...$arg One or more argument for $format.
     *
     * @return IMutableString That instance.
     */
    function appendFormat($format) : IMutableString;

    /**
     * Appends a formatted string.
     *
     * @param string $format The format string.
     * @param mixed ...$args One or more argument lists for for $format.
     *
     * @return IMutableString That instance.
     */
    function appendFormatArray($format, $args = null) : IMutableString;

    /**
     * Clears the string.
     *
     * @return IMutableString That instance.
     */
    function clear() : IMutableString;
}
