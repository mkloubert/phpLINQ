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


namespace System\Linq;


/**
 * A sequence.
 *
 * @package System\Linq
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class Enumerable extends \System\Collections\EnumerableBase {
    /**
     * Creates a new instance.
     *
     * @param mixed $items The initial items.
     *
     * @return Enumerable The new instance.
     */
    public static function create($items = null) {
        if (is_null($items)) {
            $items = new \EmptyIterator();
        }

        return new static(static::asIterator($items));
    }

    public static function createEnumerable($items = null) {
        if (is_null($items)) {
            $items = new \EmptyIterator();
        }

        return new self(static::asIterator($items));
    }

    /**
     * Creates a new instance from JSON data.
     *
     * @param string $json The JSON data.
     *
     * @return Enumerable The new instance.
     */
    public static function fromJson($json) {
        return static::create(json_decode($json, true));
    }

    /**
     * Creates a new instance from a list of values.
     *
     * @param mixed $value... The initial values.
     *
     * @return Enumerable The new instance.
     */
    public static function fromValues() {
        return static::create(func_get_args());
    }
}
