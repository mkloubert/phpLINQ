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
class String extends \System\ObjectWrapper implements \Countable, \Serializable, \IteratorAggregate {
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
        return static::formatArray($this->toString(),
                                   \func_get_args());
    }


    /**
     * {@inheritDoc}
     */
    public function count() {
        return \strlen($this->getWrappedValue());
    }

    /**
     * {@inheritDoc}
     */
    public function equals($other) {
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
        $args = Enumerable::create($args)
                          ->toArray();

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
