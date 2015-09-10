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
class StringBuilder extends \System\ClrString {
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
     * {@inheritDoc}
     */
    public function insert($index, $value) {
        $this->_wrappedValue = parent::insert($index, $value)
                                     ->getWrappedValue();

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function remove($startIndex, $length) {
        $this->_wrappedValue = parent::remove($startIndex, $length)
                                     ->getWrappedValue();

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function replace($oldValue, $newValue, $ignoreCase = false, &$count = null) {
        $this->_wrappedValue = parent::replace($oldValue, $newValue, $ignoreCase, $count)
                                     ->getWrappedValue();

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function replaceRegExp($pattern, $replacement) {
        $this->_wrappedValue = $this->invokeFuncForValue(function($str, $p, $r) {
                                                             return \preg_replace($p, $r, $str);
                                                         }, $pattern, $replacement);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toLower() {
        $this->_wrappedValue = $this->invokeFuncForValue("\\strtolower");

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toUpper() {
        $this->_wrappedValue = $this->invokeFuncForValue("\\strtoupper");

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function trimMe($func, $charlist) {
        $args = array();
        if (null !== $charlist) {
            $args[] = static::valueToString($charlist);
        }

        $this->_wrappedValue = $this->invokeFuncForValueArray($func, $args);

        return $this;
    }
}
