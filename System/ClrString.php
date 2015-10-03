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

use \System\Linq\Enumerable;
use \System\Text\StringBuilder;


/**
 * A constant string.
 *
 * @package System
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class ClrString extends Enumerable implements IString {
    /**
     * @var string
     */
    protected $_wrappedValue;


    /**
     * Initializes a new instance of that class.
     *
     * @param mixed $value The value to wrap (as string).
     */
    public function __construct($value = '') {
        $this->_wrappedValue = static::valueToString($value);

        parent::__construct($this->createStringIterator());
    }

    /**
     * {@inheritDoc}
     */
    public final function __invoke() {
        return static::formatArray($this,
                                   \func_get_args());
    }


    /**
     * {@inheritDoc}
     */
    public function asMutable() : IMutableString {
        return new StringBuilder($this->_wrappedValue);
    }

    /**
     * Converts / casts a value to a IString object.
     *
     * @param mixed $val The value to convert / cast
     * @param bool $nullAsEmpty Handle (null) as empty string or not.
     * @param bool $mutable String should be mutable or not.
     *
     * @return IString|null The (new) string object or (null) if $val is also (null) AND
     *                      $nullAsEmpty has the value (false).
     */
    public static final function asString($val, bool $nullAsEmpty = true, bool $mutable = false) {
        if (null === $val) {
            if (!$nullAsEmpty) {
                return null;
            }
        }

        if (!$val instanceof IString) {
            $val = new self($val);
        }

        if ($mutable) {
            return $val->asMutable();
        }

        return $val;
    }

    /**
     * {@inheritDoc}
     */
    public final function compareTo($other) : int {
        return \strcmp($this->_wrappedValue,
                       static::valueToString($other, false));
    }

    /**
     * {@inheritDoc}
     */
    public final function count() {
        return \strlen($this->_wrappedValue);
    }

    /**
     * Creates an iterator for the current string value.
     *
     * @return \Iterator The created iterator.
     */
    protected function createStringIterator() : \Iterator {
        return new \ArrayIterator(\str_split($this->_wrappedValue));
    }

    /**
     * Formats a string.
     *
     * @param string $format The format string.
     * @param mixed ...$arg One or more argument for $format.
     *
     * @return IString The formatted string.
     */
    public static final function format($format) : IString {
        return static::formatArray($format,
                                   \array_slice(\func_get_args(), 1));
    }

    /**
     * Formats a string.
     *
     * @param string $format The format string.
     * @param mixed ...$args One or more argument lists for $format.
     *
     * @return string The formatted string.
     */
    public static function formatArray($format, $args = null) : IString {
        if (!\is_array($args)) {
            $args = Enumerable::create($args)
                              ->toArray();
        }

        $argCount = \func_num_args();
        for ($i = 2; $i < $argCount; $i++) {
            Enumerable::create(\func_get_arg($i))
                      ->appendToArray($args);
        }

        return static::asString(\preg_replace_callback('/{(\d+)(\:[^}]*)?}/i',
                                                       function($match) use ($args) {
                                                           $i = (int)$match[1];

                                                           $format = null;
                                                           if (isset($match[2])) {
                                                               $format = \substr($match[2], 1);
                                                           }

                                                           return \array_key_exists($i, $args) ? ClrString::parseFormatStringValue($format, $args[$i])
                                                                                               : $match[0];
                                                       }, static::valueToString($format)));
    }

    /**
     * {@inheritDoc}
     */
    public final function getWrappedValue() : string {
        return $this->_wrappedValue;
    }

    /**
     * {@inheritDoc}
     */
    public final function isEmpty() : bool {
        return \strlen($this->_wrappedValue) < 1;
    }

    /**
     * {@inheritDoc}
     */
    public function isMutable() : bool {
        return false;
    }

    /**
     * Checks if a string is (null) or empty.
     *
     * @param string $str The string to check.
     *
     * @return bool Is (null) / empty or not.
     */
    public static final function isNullOrEmpty($str) : bool {
        $str = static::valueToString($str, false);

        return (null === $str) ||
               (\strlen($str) < 1);
    }

    /**
     * Checks if a string is (null) or contains whitespaces only.
     *
     * @param string $str The string to check.
     * @param string $character_mask The custom list of whitespace characters to use.
     *
     * @return bool Is (null) / empty or not.
     */
    public static final function isNullOrWhitespace($str, $character_mask = null) : bool {
        if (null === $str) {
            return true;
        }

        if (!$str instanceof IString) {
            $str = new self($str);
        }

        return $str->isWhitespace($character_mask);
    }

    /**
     * {@inheritDoc}
     */
    public final function isWhitespace($character_mask = null) : bool {
        if (null === $character_mask) {
            $trimmed = \trim($this->_wrappedValue);
        }
        else {
            $trimmed = \trim($this->_wrappedValue, $character_mask);
        }

        return \strlen($trimmed) < 1;
    }

    /**
     * {@inheritDoc}
     */
    public final function length() : int {
        return \strlen($this->_wrappedValue);
    }

    /**
     * {@inheritDoc}
     */
    public final function offsetExists($index) {
        return isset($this->_wrappedValue[$index]);
    }

    /**
     * {@inheritDoc}
     */
    public final function offsetGet($index) {
        $this->throwIfIndexOfOfRange($index);

        return $this->_wrappedValue[$index];
    }

    /**
     * {@inheritDoc}
     */
    public final function offsetSet($index, $value) {
        $this->throwIfNotMutable();
        $this->throwIfIndexOfOfRange($index);

        $this->_wrappedValue[$index] = static::valueToString($value);
    }

    /**
     * {@inheritDoc}
     */
    public final function offsetUnset($index) {
        $this->throwIfNotMutable();
        $this->throwIfIndexOfOfRange($index);

        $this->transformWrappedValue(\substr($this->_wrappedValue, 0, $index) .
                                     \substr($this->_wrappedValue, $index + 1));
    }

    /**
     * Formats a value for a formatted string.
     *
     * @param string $format The format string for $value.
     * @param mixed $value The value to parse.
     *
     * @return mixed The parsed value.
     */
    public static function parseFormatStringValue($format, $value) {
        $format = static::valueToString($format, false);

        if (null !== $format) {
            if ($value instanceof \DateTimeInterface) {
                return $value->format($format);
            }
            else if ($value instanceof \DateInterval) {
                return $value->format($format);
            }

            // default
            return \sprintf($format, $value);
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public final function startWith($expr) : bool {
        return 0 === \strpos($this->_wrappedValue,
                             static::valueToString($expr, false));
    }

    /**
     * Throws an exception if an index is out of range.
     *
     * @param int $index The value to check.
     *
     * @throws ArgumentOutOfRangeException $index is out of range.
     */
    protected final function throwIfIndexOfOfRange(int $index) {
        if (($index < 0) || ($index >= \strlen($this->_wrappedValue))) {
            throw new ArgumentOutOfRangeException($index, 'index');
        }
    }

    /**
     * Throws an exception if that string is NOT mutable.
     *
     * @throws InvalidOperationException String is NOT mutable.
     */
    protected final function throwIfNotMutable() {
        if (!$this->isMutable()) {
            throw new InvalidOperationException('String is NOT mutable!');
        }
    }

    /**
     * {@inheritDoc}
     */
    public final function toArray($keySelector = null) : array {
        return static::createEnumerable($this->_wrappedValue)
                     ->toArray($keySelector);
    }

    /**
     * {@inheritDoc}
     */
    public final function toCharArray() : array {
        return \str_split($this->_wrappedValue);
    }

    /**
     * {@inheritDoc}
     */
    public final function toLower() : IString {
        return $this->transformWrappedValue(\strtolower($this->_wrappedValue));
    }

    /**
     * {@inheritDoc}
     */
    public final function toUpper() : IString {
        return $this->transformWrappedValue(\strtoupper($this->_wrappedValue));
    }

    /**
     * {@inheritDoc}
     */
    public function toString() : IString {
        return new static($this->_wrappedValue);
    }

    /**
     * Transforms the wrapped value.
     *
     * @param string $newValue The new value.
     *
     * @return IString The (new) string.
     */
    protected function transformWrappedValue($newValue) : IString {
        $result = new static($newValue);

        // try to move to same position
        while ($result->valid()) {
            if ($result->key() === $this->key()) {
                break;
            }

            $result->next();
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public final function trim($character_mask = null) : IString {
        return $this->trimMe("\\trim", $character_mask);
    }

    /**
     * {@inheritDoc}
     */
    public final function trimEnd($character_mask = null) : IString {
        return $this->trimMe("\\rtrim", $character_mask);
    }

    /**
     * Trims the string.
     *
     * @param callable $func The function to use.
     * @param string $character_mask The custom char list that represent whitespaces.
     *
     * @return IString The (new) string.
     */
    protected final function trimMe(callable $func, $character_mask) : IString {
        if (null === $character_mask) {
            $trimmed = $func($this->_wrappedValue);
        }
        else {
            $trimmed = $func($this->_wrappedValue, $character_mask);
        }

        return $this->transformWrappedValue($trimmed);
    }

    /**
     * {@inheritDoc}
     */
    public final function trimStart($character_mask = null) : IString {
        return $this->trimMe("\\ltrim", $character_mask);
    }

    /**
     * Updates the inner iterator.
     */
    protected final function updateStringIterator() {
        $this->_i = $this->createStringIterator();
    }

    /**
     * Converts a value to a PHP string.
     *
     * @param mixed $value The input value.
     * @param bool $nullAsEmpty Handle (null) as empty or not.
     *
     * @return string $value as string.
     */
    public static function valueToString($value, bool $nullAsEmpty = true) {
        if (\is_string($value)) {
            return $value;
        }

        if (null === $value) {
            return $nullAsEmpty ? '' : null;
        }

        return \strval($value);
    }
}
