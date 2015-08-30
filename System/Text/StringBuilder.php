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

namespace System\Text;

use \System\Linq\Enumerable;


/**
 * A mutable string.
 *
 * @package System\Text
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class StringBuilder extends \System\String {
    /**
     * Appends a value.
     *
     * @param mixed $value The value to append.
     *
     * @return $this
     */
    public function append($value) {
        $this->_wrappedValue .= static::valueToString($value);
        return $this;
    }

    /**
     * Appends a formatted string.
     *
     * @param string $format The format string.
     * @param mixed ...$arg One or more argument for $format.
     *
     * @return $this
     */
    public function appendFormat($format) {
        return $this->append(\call_user_func_array(array(__CLASS__, "format"),
                                                   \func_get_args()));
    }

    /**
     * Appends a formatted string.
     *
     * @param string $format The format string.
     * @param mixed $args One or more argument for $format.
     *
     * @return $this
     */
    public function appendFormatArray($format, $args) {
        return $this->append(static::formatArray($format, $args));
    }

    /**
     * Appends a value and additionally appends a new line expression.
     *
     * @param mixed $value The optional value to append.
     *
     * @return $this
     */
    public function appendLine($value = null) {
        return $this->append($value)
                    ->append(\PHP_EOL);
    }

    /**
     * Appends a list of values.
     *
     * @param mixed ...$value One or more value to append.
     *
     * @return $this
     */
    public function appendValues() {
        return $this->appendValuesArray(\func_get_args());
    }

    /**
     * Appends a list of values.
     *
     * @param mixed $values The values to append.
     *
     * @return $this
     */
    public function appendValuesArray($values = null) {
        foreach (Enumerable::create($values) as $v) {
            $this->append($v);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function asMutable() {
        return $this;
    }

    /**
     * Resets the current value.
     *
     * @return $this
     */
    public function clear() {
        $this->_wrappedValue = '';
        return $this;
    }

    /**
     * Inserts a value.
     *
     * @param int $index The index where the value should be inserted.
     * @param mixed $value The value to insert.
     *
     * @return $this
     *
     * @throws \System\ArgumentOutOfRangeException $index is invalid.
     */
    public function insert($index, $value) {
        $len = $this->count();

        if (($index < 0) || ($index > $len)) {
            throw new \System\ArgumentOutOfRangeException('index', $index);
        }

        $newStr = \substr($this->getWrappedValue(), 0, $index) .
                  static::valueToString($value);

        if ($index < $len) {
            $newStr .= \substr($this->getWrappedValue(), $index);
        }

        $this->_wrappedValue = $newStr;
        return $this;
    }

    /**
     * Removes a part from the current string.
     *
     * @param int $startIndex The zero based start index.
     * @param int $length The length.
     *
     * @return $this
     *
     * @throws \System\ArgumentOutOfRangeException $startIndex or the combination of $startIndex and $length
     *                                             are invalid.
     */
    public function remove($startIndex, $length) {
        $curLen = $this->count();

        if (($startIndex < 0) || ($startIndex > $curLen)) {
            throw new \System\ArgumentOutOfRangeException('startIndex', $curLen);
        }

        $endIndex = $startIndex + $length;
        if ($endIndex > $curLen) {
            throw new \System\ArgumentOutOfRangeException('length', $endIndex);
        }

        $newStr = \substr($this->getWrappedValue(), 0, $startIndex);
        if ($endIndex < $curLen) {
            $newStr .= \substr($this->getWrappedValue(), $endIndex);
        }

        $this->_wrappedValue = $newStr;
        return $this;
    }

    /**
     * Replaces one or more expressions in that string.
     *
     * @param string $oldValue The value to search for.
     * @param string $newValue The new value.
     * @param bool $ignoreCase Ignore case or not.
     * @param int &$count The variable where to write how many expressions were replaced.
     *
     * @return $this
     */
    public function replace($oldValue, $newValue, $ignoreCase = false, &$count = null) {
        $func = !$ignoreCase ? "\\str_replace" : "\\str_ireplace";

        $this->_wrappedValue = \call_user_func_array($func,
                                                     array($oldValue,
                                                           static::valueToString($newValue),
                                                           $this->getWrappedValue(),
                                                           &$count));
        return $this;
    }

    /**
     * Replaces parts of that string by using a regular expression (s. \preg_replace()).
     *
     * @param mixed $pattern The regular expression.
     * @param mixed $replacement The replacement.
     *
     * @return $this
     */
    public function replaceRegExp($pattern, $replacement) {
        $this->_wrappedValue = \preg_replace($pattern, $replacement, $this->getWrappedValue());
        return $this;
    }
}
