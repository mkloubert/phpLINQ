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
final class Enumerable extends \System\Collections\EnumerableBase {
    /**
     * Builds a new sequence by using a factory function.
     *
     * @param int $count The number of items to build.
     * @param callable $itemFactory The function that builds an item.
     *
     * @return static The new sequence.
     */
    public static function build($count, $itemFactory) {
        $items = array();

        $index = 0;
        while ($index < $count) {
            $items[] = call_user_func($itemFactory,
                                      $index++);
        }

        return static::createEnumerable($items);
    }

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

    /**
     * Creates a sequence with a range of numbers.
     *
     * @param number $start The start value.
     * @param number $count The number of items.
     * @param int|callable $increaseBy The increase value or the function that provides that value.
     *
     * @return static The new sequence.
     */
    public static function range($start, $count, $increaseBy = 1) {
        $increaseFunc = $increaseBy;
        if (!is_callable($increaseFunc)) {
            $increaseFunc = function() use ($increaseBy) {
                return $increaseBy;
            };
        }

        return static::build($count,
                             function($index) use (&$start, $increaseFunc) {
                                 $result = $start;

                                 $start += call_user_func($increaseFunc,
                                                          $result, $index);

                                 return $result;
                             });
    }
}
