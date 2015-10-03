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

namespace System;

use \System\Collections\IEnumerable;


/**
 * Describes a string.
 *
 * @package System
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
interface IString extends \ArrayAccess, IComparable, IEnumerable, IValueWrapper {
    /**
     * Appends a value.
     *
     * @param mixed ...$value One or more value to append.
     *
     * @return IString The (new) string.
     */
    function append($value) : IString;

    /**
     * Appends a list of values.
     *
     * @param mixed ...$values One or more value lists to append.
     *
     * @return IString The (new) string.
     */
    function appendArray($values = null) : IString;

    /**
     * Appends a formatted string.
     *
     * @param string $format The format string.
     * @param mixed ...$arg One or more argument for $format.
     *
     * @return IString The (new) string.
     */
    function appendFormat($format) : IString;

    /**
     * Appends a formatted string.
     *
     * @param string $format The format string.
     * @param mixed ...$args One or more argument lists for for $format.
     *
     * @return IString The (new) string.
     */
    function appendFormatArray($format, $args = null) : IString;

    /**
     * Returns a formatted string by using that instance as format string.
     *
     * @param mixed ...$arg One or more argument for the format string.
     *
     * @return IString The formatted string.
     */
    function __invoke();

    /**
     * Returns that string as mutable version.
     *
     * @return IMutableString Mutable string.
     */
    function asMutable() : IMutableString;

    /**
     * Checks if that contains another.
     *
     * @param string $str The string to search for.
     * @param bool|int $ignoreCaseOrOffset Ignore case or not.
     *                                     If only two arguments are submitted and this value is an integer,
     *                                     it is used as value for $offset and set to default (false).
     * @param int $offset The custom offset from where to start.
     *
     * @return bool Conatins $str or not.
     */
    function containsString($str, $ignoreCaseOrOffset = false, int $offset = 0) : bool;

    /**
     * Checks if that string ends with an expression.
     *
     * @param string $expr The expression to check.
     * @param bool $ignoreCase Ignore case or not.
     *
     * @return bool Ends with expression or not.
     */
    function endsWith($expr, bool $ignoreCase = false) : bool;

    /**
     * {@inheritDoc}
     */
    function getWrappedValue() : string;

    /**
     * Inserts a value.
     *
     * @param $startIndex The zero based start index.
     * @param mixed $value The value to insert.
     *
     * @return IString The (new) string.
     */
    function insert(int $startIndex, $value) : IString;

    /**
     * Inserts a list of values.
     *
     * @param $startIndex The zero based start index.
     * @param mixed ....$values One or more value list to insert.
     *
     * @return IString The (new) string.
     */
    function insertArray(int $startIndex, $values) : IString;

    /**
     * Gets if the string is mutable or not.
     *
     * @return bool Is mutable or not.
     */
    function isMutable() : bool;

    /**
     * Checks if that string contains whitespaces only.
     *
     * @param string $character_mask The custom list of whitespace characters to use.
     *
     * @return bool Contains whitespaces only or not.
     */
    function isWhitespace($character_mask = null) : bool;

    /**
     * Gets the length of the string.
     *
     * @return int The length.
     */
    function length() : int;

    /**
     * Pad that string to a certain length with another string (beginning and end).
     *
     * @param int $pad_length s. \str_pad()
     * @param string|null $pad_string s. \str_pad(); if (null), it is converted to ' '
     *
     * @return IString The (new) string.
     */
    function pad(int $pad_length, $pad_string = null) : IString;

    /**
     * Pad that string to a certain length with another string (beginning).
     *
     * @param int $pad_length s. \str_pad()
     * @param string|null $pad_string s. \str_pad(); if (null), it is converted to ' '
     *
     * @return IString The (new) string.
     */
    function padLeft(int $pad_length, $pad_string = null) : IString;

    /**
     * Pad that string to a certain length with another string (end).
     *
     * @param int $pad_length s. \str_pad()
     * @param string|null $pad_string s. \str_pad(); if (null), it is converted to ' '
     *
     * @return IString The (new) string.
     */
    function padRight(int $pad_length, $pad_string = null) : IString;

    /**
     * Prepends a value.
     *
     * @param mixed $value The value to prepend.
     *
     * @return IString The (new) string.
     */
    function prepend($value) : IString;

    /**
     * Prepends a list of values.
     *
     * @param mixed ...$values One or more value lists to prepend.
     *
     * @return IString The (new) string.
     */
    function prependArray($values = null) : IString;

    /**
     * Prepends a formatted string.
     *
     * @param string $format The format string.
     * @param mixed ...$arg One or more argument for $format.
     *
     * @return IString The (new) string.
     */
    function prependFormat($format) : IString;

    /**
     * Prepends a formatted string.
     *
     * @param string $format The format string.
     * @param mixed ...$args One or more argument lists for for $format.
     *
     * @return IString The (new) string.
     */
    function prependFormatArray($format, $args = null) : IString;

    /**
     * Replaces parts inside that string.
     *
     * @param string $oldValue The value value.
     * @param string $newValue The new value.
     * @param bool $ignoreCase Ignore case or not.
     * @param int &$count The variable were to write down how many replacements happend.
     *
     * @return IString The (new) string.
     */
    function replace($oldValue, $newValue, bool $ignoreCase = false, &$count = null) : IString;

    /**
     * Splits the string.
     *
     * @param string $delimiter The delimiter.
     * @param int $limit The limit, if defined.
     *
     * @return IEnumerable List of IString instances.
     */
    function split($delimiter, $limit = null) : IEnumerable;

    /**
     * Extracts a part from that string.
     *
     * @param int $startIndex The start index.
     * @param int|null $length
     *
     * @return IString The (new) string.
     */
    function subString(int $startIndex, $length = null) : IString;

    /**
     * Checks if that string starts with an expression.
     *
     * @param string $expr The expression to check.
     * @param bool $ignoreCase Ignore case or not.
     *
     * @return bool Starts with expression or not.
     */
    function startsWith($expr, bool $ignoreCase = false) : bool;

    /**
     * Converts the string to a char array.
     *
     * @return string[] The string as char array.
     */
    function toCharArray() : array;

    /**
     * Returns a version of that string with lowercase chars.
     *
     * @return IString The (new) string.
     */
    function toLower() : IString;

    /**
     * Returns a version of that string with uppercase chars.
     *
     * @return IString The (new) string.
     */
    function toUpper() : IString;

    /**
     * Strip whitespace (or other characters) from the beginning and end of the string.
     *
     * @param string $character_mask The custom list of characters to strip.
     *
     * @return IString The (new) string.
     */
    function trim($character_mask = null) : IString;

    /**
     * Strip whitespace (or other characters) from the end of the string.
     *
     * @param string $character_mask The custom list of characters to strip.
     *
     * @return IString The (new) string.
     */
    function trimEnd($character_mask = null) : IString;

    /**
     * Strip whitespace (or other characters) from the beginning of the string.
     *
     * @param string $character_mask The custom list of characters to strip.
     *
     * @return IString The (new) string.
     */
    function trimStart($character_mask = null) : IString;
}
