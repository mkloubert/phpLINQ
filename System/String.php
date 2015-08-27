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

use \System\Linq\Enumerable;


/**
 * A constant string.
 *
 * @package System
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class String extends \System\ObjectWrapper implements \ArrayAccess,\Countable, IComparable, \IteratorAggregate, \Serializable {
    /**
     * Value for an index that tells that a string was not found.
     */
    const NOT_FOUND_INDEX = -1;


    /**
     * Initializes a new instance of that class.
     *
     * @param string $value The value.
     */
    public function __construct($value = null) {
        parent::__construct(static::valueToString($value));
    }

    /**
     * Handles that string as format string and returns it as formatted string.
     *
     * @param mixed ...$arg One or more argument for that string.
     *
     * @return string The formatted string.
     */
    public function __invoke() {
        return static::formatArray($this->getWrappedValue(),
                                   \func_get_args());
    }


    /**
     * Returns a value as a String object.
     *
     * @param mixed $val The value to convert/cast.
     * @param bool $nullAsEmpty If (true) an empty string will be returned instead of a (null) reference.
     *
     * @return static
     */
    public static function asString($val, $nullAsEmpty = true) {
        if (null === $val) {
            if ($nullAsEmpty) {
                $val = '';
            }
        }

        if (null === $val) {
            return null;
        }

        if ($val instanceof static) {
            return $val;
        }

        return new static($val);
    }

    /**
     * {@inheritDoc}
     */
    public function compareTo($other) {
        return \strcmp($this, $other);
    }

    /**
     * Checks if that string contains an expression.
     *
     * @param string $expr The expression to search for.
     * @param bool $ignoreCase Ignore case or not.
     *
     * @return bool String contains expression or not.
     */
    public function contains($expr, $ignoreCase = false) {
        return false !== $this->invokeFindStringFunc($expr, $ignoreCase);
    }

    /**
     * Creates a string that is stored in a specific encoding (s. \iconv()).
     *
     * @param mixed $str The string to convert.
     * @param string $srcEnc The source encoding. If not defined the input encoding is used.
     * @param string $targetEnc The target encoding. If not defined the internal encoding is used.
     *
     * @return static
     */
    public static function convertFrom($str, $srcEnc = null, $targetEnc = null) {
        if (static::isNullOrWhitespace($srcEnc)) {
            $srcEnc = \iconv_get_encoding('input_encoding');
        }

        if (static::isNullOrWhitespace($targetEnc)) {
            $targetEnc = \iconv_get_encoding('internal_encoding');
        }

        return new static(\iconv($srcEnc, $targetEnc,
                                 static::valueToString($str)));
    }

    /**
     * Converts a string to a new encoding (s. \iconv()).
     *
     * @param string $targetEnc The target encoding. If not defined the output encoding is used.
     * @param string $srcEnc The source encoding. If not defined the internal encoding is used.
     *
     * @return static
     */
    public function convertTo($targetEnc = null, $srcEnc = null) {
        if (static::isNullOrWhitespace($targetEnc)) {
            $targetEnc = \iconv_get_encoding('output_encoding');
        }

        if (static::isNullOrWhitespace($srcEnc)) {
            $srcEnc = \iconv_get_encoding('internal_encoding');
        }

        return new static(\iconv($srcEnc, $targetEnc,
                                 $this->getWrappedValue()));
    }

    /**
     * {@inheritDoc}
     */
    public function count() {
        return \strlen($this->getWrappedValue());
    }

    /**
     * Checks if that strings ends with a specific expression.
     *
     * @param string $expr The expression to check.
     * @param bool $ignoreCase Ignore case or not.
     *
     * @return bool Ends with expression or not.
     */
    public function endsWith($expr, $ignoreCase = false) {
        $expr = static::valueToString($expr);

        return ('' === $expr) ||
               (($temp = $this->length() - \strlen($expr)) >= 0 &&
                false !== $this->invokeFindStringFunc($expr, $ignoreCase, $temp));
    }

    /**
     * {@inheritDoc}
     */
    public function equals($other) {
        if (\is_string($other)) {
            return $this->getWrappedValue() === $other;
        }

        if ($other instanceof \System\ObjectWrapper) {
            return $this->getWrappedValue() === $other->getWrappedValue();
        }

        return $this === $other;
    }

    /**
     * Formats a string.
     *
     * @param string $format The format string.
     * @param mixed ...$arg One or more argument for $format.
     *
     * @return string The formatted string.
     */
    public static function format($format) {
        return static::formatArray($format,
                                   \array_slice(\func_get_args(), 1));
    }

    /**
     * Formats a string.
     *
     * @param string $format The format string.
     * @param mixed $args One or more argument for $format.
     *
     * @return string The formatted string.
     */
    public static function formatArray($format, $args) {
        if (!\is_array($args)) {
            $args = Enumerable::create($args)
                              ->toArray();
        }

        return \preg_replace_callback('/{(\d+)(\:[^}]*)?}/i',
                                      function($match) use ($args) {
                                          $i = (int)$match[1];

                                          $format = null;
                                          if (isset($match[2])) {
                                              $format = \substr($match[2], 1);
                                          }

                                          return \array_key_exists($i, $args) ? String::parseFormatStringValue($format, $args[$i])
                                                                              : $match[0];
                                      }, $format);
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator() {
        return Enumerable::create($this->getWrappedValue());
    }

    /**
     * Finds the first occurrence of a string expression.
     *
     * @param string $expr The string to search for.
     * @param bool $ignoreCase Ignore case or not.
     * @param int $offset The offset.
     *
     * @return int The zero based index or -1 if not found.
     */
    public function indexOf($expr, $ignoreCase = false, $offset = 0) {
        $result = $this->invokeFindStringFunc($expr, $ignoreCase, $offset);

        return false !== $result ? $result
                                 : static::NOT_FOUND_INDEX;
    }

    /**
     * Finds the first occurrence of a char list.
     *
     * @param $chars The list of chars.
     * @param bool $ignoreCase Ignore case or not.
     * @param int $offset The offset.
     *
     * @return int The zero based index or -1 if not found.
     */
    public function indexOfAny($chars, $ignoreCase = false, $offset = 0) {
        $chars = static::valueToString($chars);
        $charCount = \strlen($chars);

        $result = static::NOT_FOUND_INDEX;

        for ($i = 0; $i < $charCount; $i++) {
            $result = $this->indexOf($chars[$i], $ignoreCase, $offset);

            if (static::NOT_FOUND_INDEX != $result) {
                // found
                break;
            }
        }

        return $result;
    }

    /**
     * Invokes the function for finding a string.
     *
     * @param string &$expr The expression to search for.
     * @param bool $ignoreCase Ignore case or not.
     * @param int $offset The offset.
     *
     * @return mixed The result of the invocation.
     */
    protected function invokeFindStringFunc(&$expr = null, $ignoreCase = false, $offset = 0) {
        $expr = static::valueToString($expr);
        $str = $this->getWrappedValue();
        $func = !$ignoreCase ? 'strpos' : 'stripos';

        return \call_user_func($func,
                               $str, $expr, $offset);
    }

    /**
     * Gets if the string is empty or not.
     *
     * @return bool Is empty or not.
     */
    public function isEmpty() {
        return $this->length() < 1;
    }

    /**
     * Gets if the string is NOT empty.
     *
     * @return bool Is empty (false) or not (true).
     */
    public function isNotEmpty() {
        return !$this->isEmpty();
    }

    /**
     * Checks if a string is (null) or empty.
     *
     * @param string $str The string to check.
     *
     * @return bool Is (null)/empty or not.
     */
    public static function isNullOrEmpty($str) {
        return (null === $str) ||
               ('' === static::valueToString($str));
    }

    /**
     * Checks if a string is (null) or contains whitespaces only.
     *
     * @param string $str The string to check.
     * @param string $charlist The custom list of chars that represent whitespaces (s. \\trim()).
     *
     * @return bool Is (null)/contains whitespaces only or not.
     */
    public static function isNullOrWhitespace($str, $charlist = null) {
        if (null === $str) {
            return true;
        }

        $args = array(static::valueToString($str));
        if (!\is_null($charlist)) {
            $args[] = static::valueToString($charlist);
        }

        return '' === \call_user_func_array("\\trim", $args);
    }

    /**
     * Gets the length of the string.
     *
     * @return int The length of that string.
     */
    public function length() {
        return $this->count();
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($index) {
        return ($index >= 0) &&
               ($index < $this->length());
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($index) {
        $this->throwIfIndexOutOfRange($index);

        return $this->_wrappedValue[$index];
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($index, $value) {
        $this->throwIfIndexOutOfRange($index);

        $this->_wrappedValue[$index] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($index) {
        $this->throwIfIndexOutOfRange($index);

        $this->_wrappedValue[$index] = "\\0";
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
        if (null !== $format) {
            $handled = true;
            $format = static::valueToString($format);

            if ($value instanceof \DateTime) {
                $value = $value->format($format);
            }
            else {
                $handled = false;
            }

            if (!$handled) {
                // default
                $value = \sprintf($format, $value);
            }
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize() {
        return $this->toString();
    }

    /**
     * Checks if that strings starts with a specific expression.
     *
     * @param string $expr The expression to check.
     * @param bool $ignoreCase Ignore case or not.
     *
     * @return bool Starts with expression or not.
     */
    public function startsWith($expr, $ignoreCase = false) {
        return 0 === $this->invokeFindStringFunc($expr, $ignoreCase);
    }

    /**
     * Throws an exception if an index value is out of range.
     *
     * @param int $index The value to check.
     *
     * @throws ArgumentOutOfRangeException
     */
    protected function throwIfIndexOutOfRange($index) {
        if (($index < 0) || ($index >= $this->length())) {
            throw new ArgumentOutOfRangeException('index');
        }
    }

    /**
     * Converts that string to an array of its chars.
     *
     * @return array The string as char array.
     */
    public function toCharArray() {
        return $this->getIterator()
                    ->toArray();
    }

    /**
     * Converts that string to lower chars.
     *
     * @return static
     */
    public function toLower() {
        return new static(\strtolower($this->getWrappedValue()));
    }

    /**
     * Converts that string to upper chars.
     *
     * @return static
     */
    public function toUpper() {
        return new static(\strtoupper($this->getWrappedValue()));
    }

    /**
     * Trims that string at the beginning and the end.
     *
     * @param string $charlist The list of chars that represents whitespaces.
     *
     * @return static
     */
    public function trim($charlist = null) {
        return $this->trimMe("\\trim", $charlist);
    }

    /**
     * Trims that string at the end.
     *
     * @param string $charlist The list of chars that represents whitespaces.
     *
     * @return static
     */
    public function trimEnd($charlist = null) {
        return $this->trimMe("\\rtrim", $charlist);
    }

    /**
     * Trims that string.
     *
     * @param callable $func The function to use.
     * @param string $charlist The list of chars that represents whitespaces.
     *
     * @return static
     */
    protected function trimMe($func, $charlist) {
        $args = array(static::valueToString($this->getWrappedValue()));
        if (!\is_null($charlist)) {
            $args[] = static::valueToString($charlist);
        }

        return new static(\call_user_func_array($func, $args));
    }

    /**
     * Trims that string at the beginning.
     *
     * @param string $charlist The list of chars that represents whitespaces.
     *
     * @return static
     */
    public function trimStart($charlist = null) {
        return $this->trimMe("\\ltrim", $charlist);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized) {
        $this->_wrappedValue = static::valueToString($serialized);
    }

    /**
     * Converts a value to a string.
     *
     * @param mixed $value The input value.
     *
     * @return string The output value.
     */
    public static function valueToString($value) {
        return \strval($value);
    }
}
