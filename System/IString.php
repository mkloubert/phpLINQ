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
use \System\IO\IOException;
use \System\IO\IStream;
use \System\IO\StreamClosedException;


/**
 * Describes a string.
 *
 * @package System
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
interface IString extends \ArrayAccess, IComparable, IEnumerable, IValueWrapper {
    /**
     * Returns a formatted string by using that instance as format string.
     *
     * @param mixed ...$arg One or more argument for the format string.
     *
     * @return IString The formatted string.
     */
    function __invoke();


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
     * Invokes a buffered action and appends the content and result.
     *
     * @param callable $action The action to invoke.
     * @param bool|callable $startNewOrBufferFunc Start new buffer or not.
     *                                            If only one argument is submitted and that value is a callable
     *                                            it will be used as value for $bufferFunc and set to default (true).
     * @param callable $bufferFunc The optional callable to use for the NEW buffer.
     *
     * @return IString The (new) string.
     *
     * @throws ArgumentException $action / $bufferFunc is no valid callable / lambda expression.
     */
    function appendBuffer($action, $startNewOrBufferFunc = true, $bufferFunc = null) : IString;

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
     * Appends a value with a new line expression.
     *
     * @param string $value The optional value to append.
     * @param string|bool $newLine The custom new line expression to use.
     *                             If (true) the value from \PHP_EOL constant is used.
     *
     * @return IString The (new) string.
     */
    function appendLine($value = '', $newLine = null) : IString;

    /**
     * Loads data from a stream and appends it.
     *
     * @param IStream $stream The source stream.
     * @param int $bufferSize The buffer size to use for the read operation.
     * @param int|null $count The maximum number of data to load.
     *
     * @return IString The (new) stream.
     *
     * @throws ArgumentOutOfRangeException $bufferSize is less than 1
     *                                     -- or ---
     *                                     $count is defined and less than 0.
     * @throws IOException Read operation failed.
     * @throws NotSupportedException Stream is not readable.
     * @throws ObjectDisposedException Stream has been disposed.
     * @throws StreamClosedException Stream has been closed.
     */
    function appendStream(IStream $stream, int $bufferSize = 1024, $count = null) : IString;

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
     * Finds the first occurrence of a string inside that string.
     *
     * @param string $str The string to search for.
     * @param bool|int $ignoreCaseOrOffset Ignore case or not.
     *                                     If only two arguments are submitted and this value is an integer,
     *                                     it is used as value for $offset and set to default (false).
     * @param int $offset The custom offset from where to start.
     *
     * @return int The zero based index or -1 if not found.
     */
    function indexOf($searchFor, $ignoreCaseOrOffset = false, int $offset = 0) : int;

    /**
     * Inserts a value.
     *
     * @param int $startIndex The zero based start index.
     * @param mixed $value The value to insert.
     *
     * @return IString The (new) string.
     *
     * @throws ArgumentOutOfRangeException $startIndex is invalid.
     */
    function insert(int $startIndex, $value) : IString;

    /**
     * Inserts a list of values.
     *
     * @param int $startIndex The zero based start index.
     * @param mixed ....$values One or more value list to insert.
     *
     * @return IString The (new) string.
     *
     * @throws ArgumentOutOfRangeException $startIndex is invalid.
     */
    function insertArray(int $startIndex, $values) : IString;

    /**
     * Invokes a buffered action and inserts the content and result.
     *
     * @param int $startIndex The zero based start index.
     * @param callable $action The action to invoke.
     * @param bool|callable $startNewOrBufferFunc Start new buffer or not.
     *                                            If only one argument is submitted and that value is a callable
     *                                            it will be used as value for $bufferFunc and set to default (true).
     * @param callable $bufferFunc The optional callable to use for the NEW buffer.
     *
     * @return IString The (new) string.
     *
     * @throws ArgumentException $action / $bufferFunc is no valid callable / lambda expression.
     * @throws ArgumentOutOfRangeException $startIndex is invalid.
     */
    function insertBuffer(int $startIndex, $action, $startNewOrBufferFunc = true, $bufferFunc = null) : IString;

    /**
     * Inserts a value with a new line expression.
     *
     * @param string $value The optional value to insert.
     * @param string|bool $newLine The custom new line expression to use.
     *                             If (true) the value from \PHP_EOL constant is used.
     *
     * @return IString The (new) string.
     *
     * @throws ArgumentOutOfRangeException $startIndex is invalid.
     */
    function insertLine(int $startIndex, $value = '', $newLine = null) : IString;

    /**
     * Loads data from a stream and inserts it.
     *
     * @param int $startIndex The zero based start index.
     * @param IStream $stream The source stream.
     * @param int $bufferSize The buffer size to use for the read operation.
     * @param int|null $count The maximum number of data to load.
     *
     * @return IString The (new) stream.
     *
     * @throws ArgumentOutOfRangeException $startIndex is invalid
     *                                     -- or --
     *                                     $bufferSize is less than 1
     *                                     -- or --
     *                                     $count is defined and less than 0
     * @throws IOException Read operation failed.
     * @throws NotSupportedException Stream is not readable.
     * @throws ObjectDisposedException Stream has been disposed.
     * @throws StreamClosedException Stream has been closed.
     */
    function insertStream(int $startIndex, IStream $stream, int $bufferSize = 1024, $count = null) : IString;

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
     * Finds the last occurrence of a string inside that string.
     *
     * @param string $str The string to search for.
     * @param bool|int $ignoreCaseOrOffset Ignore case or not.
     *                                     If only two arguments are submitted and this value is an integer,
     *                                     it is used as value for $offset and set to default (false).
     * @param int $offset The custom offset from where to start.
     *
     * @return int The zero based index or -1 if not found.
     */
    function lastIndexOf($searchFor, $ignoreCaseOrOffset = false, int $offset = 0) : int;

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
     * Invokes a buffered action and prepends the content and result.
     *
     * @param callable $action The action to invoke.
     * @param bool|callable $startNewOrBufferFunc Start new buffer or not.
     *                                            If only one argument is submitted and that value is a callable
     *                                            it will be used as value for $bufferFunc and set to default (true).
     * @param callable $bufferFunc The optional callable to use for the NEW buffer.
     *
     * @return IString The (new) string.
     *
     * @throws ArgumentException $action / $bufferFunc is no valid callable / lambda expression.
     */
    function prependBuffer($action, $startNewOrBufferFunc = true, $bufferFunc = null) : IString;

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
     * Prepends a value with a new line expression.
     *
     * @param string $value The optional value to prepend.
     * @param string|bool $newLine The custom new line expression to use.
     *                             If (true) the value from \PHP_EOL constant is used.
     *
     * @return IString The (new) string.
     */
    function prependLine($value = '', $newLine = null) : IString;

    /**
     * Loads data from a stream and prepends it.
     *
     * @param IStream $stream The source stream.
     * @param int $bufferSize The buffer size to use for the read operation.
     * @param int|null $count The maximum number of data to load.
     *
     * @return IString The (new) stream.
     *
     * @throws ArgumentOutOfRangeException $bufferSize is less than 1
     *                                     -- or ---
     *                                     $count is defined and less than 0.
     * @throws IOException Read operation failed.
     * @throws NotSupportedException Stream is not readable.
     * @throws ObjectDisposedException Stream has been disposed.
     * @throws StreamClosedException Stream has been closed.
     */
    function prependStream(IStream $stream, int $bufferSize = 1024, $count = null) : IString;

    /**
     * Removes a part from that string.
     *
     * @param int $startIndex The zero based start index.
     * @param int $count The optional number of chars to remove.
     *
     * @return IString The (new) string.
     *
     * @throws ArgumentOutOfRangeException $startIndex / $count is less than 0
     *                                     -- or --
     *                                     Sum of $startIndex and $count is out of range
     */
    function remove(int $startIndex, $count = null) : IString;

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
     * Checks the similarity between that string and another.
     *
     * @param string $other The other string.
     * @param bool $ignoreCase Ignore case or not. If (true) chars of strings will be transformed to lowercase.
     * @param bool $doTrim Trim strings or not.
     * @param string $character_mask The custom character mask to use for the string operation.
     *
     * @return float The similarity (between 0 for 0% and 1 for 100%).
     *               0 is retured if two strings are empty.
     */
    function similarity($other, bool $ignoreCase = false, bool $doTrim = false, $character_mask = null) : float;

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
