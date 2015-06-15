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


namespace System\Collections;


/**
 * Describes a sequence.
 *
 * @package System\Collections
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
interface IEnumerable extends \Countable, \Iterator, \Serializable {
    /**
     * Applies an accumulator function over the sequence.
     *
     * @param callable $accumulator An accumulator function to be invoked on each element.
     * @param mixed $defValue The value to return if sequence is empty.
     *
     * @return mixed The final accumulator value.
     */
    function aggregate($accumulator, $defValue = null);

    /**
     * Concats the items of that sequence with one or more others.
     *
     * @param mixed $items... The items to append.
     *
     * @return IEnumerable The new sequence.
     */
    function concat();

    /**
     * Concats the items of that sequence with a list of values.
     *
     * @param mixed $items... The items to append.
     *
     * @return IEnumerable The new sequence.
     */
    function concatValues();

    /**
     * Removes duplicates.
     *
     * @param callable|null $equalityComparer The custom equality comparer.
     *
     * @return IEnumerable The new sequence.
     */
    function distinct($equalityComparer = null);

    /**
     * Gets the maximum value of that sequence.
     *
     * @param mixed $defValue The default value if sequence is empty.
     * @param callable $comparer The custom comparer to use.
     *
     * @return mixed The maximum value.
     */
    function max($defValue = null, $comparer = null);

    /**
     * Gets the minimum value of that sequence.
     *
     * @param mixed $defValue The default value if sequence is empty.
     * @param callable $comparer The custom comparer to use.
     *
     * @return mixed The maximum value.
     */
    function min($defValue = null, $comparer = null);

    /**
     * Calculates the product of the items.
     *
     * @param mixed $defValue The default value if sequence is empty.
     *
     * @return mixed The product of the items.
     */
    function product($defValue = null);

    /**
     * Extension of \Iterator::rewind() that returns the sequence itself after operation.
     *
     * @return IEnumerable
     */
    function reset();

    /**
     * Gets the runtime version the sequence is designed for.
     *
     * @return string The runtime version.
     */
    function runtimeVersion();

    /**
     * Projects each element of that sequence to an IEnumerable
     * and flattens the resulting sequences into one sequence.
     *
     * @param callable $selector The selector to use.
     *
     * @return IEnumerable The new sequence.
     */
    function selectMany($selector);

    /**
     * Skip a specific number of items in that sequence.
     *
     * @param int $count The number of items to skip.
     *
     * @return IEnumerable That instance.
     */
    function skip($count);

    /**
     * Skips the first items of that sequence while they are match a condition.
     *
     * @param callable $predicate The predicate.
     *
     * @return IEnumerable The new instance.
     */
    function skipWhile($predicate);

    /**
     * Calculates the sum of the items.
     *
     * @param mixed $defValue The default value if sequence is empty.
     *
     * @return mixed The sum of the items.
     */
    function sum($defValue = null);

    /**
     * Takes a specific number of items from that sequence.
     *
     * @param int $count The number of items to take.
     *
     * @return IEnumerable The new instance.
     */
    function take($count);

    /**
     * Takes the first items of that sequence while they are match a condition.
     *
     * @param callable $predicate The predicate.
     *
     * @return IEnumerable The new instance.
     */
    function takeWhile($predicate);

    /**
     * Converts that sequence to a PHP array.
     *
     * @param callable|null $keySelector The custom key selector.
     *
     * @return array The sequence as array.
     */
    function toArray($keySelector = null);

    /**
     * Converts that sequence to a JSON string.
     *
     * @param callable|null $keySelector The custom key selector.
     * @param int|null $options s. \json_encode()
     *
     * @return string The sequence as JSON string.
     */
    function toJson($keySelector = null, $options = null);

    /**
     * Produces the set union of that sequence and another.
     *
     * @param mixed $second The other sequence.
     * @param callable $equalityComparer The custom equality comparer to use.
     *
     * @return IEnumerable The new sequence.
     */
    function union($second, $equalityComparer = null);

    /**
     * Filters the items of that sequence.
     *
     * @param $predicate
     *
     * @return IEnumerable The filtered sequence.
     */
    function where($predicate);

    /**
     * Applies a specified function to the corresponding elements of that sequence and another,
     * producing a sequence of the results.
     *
     * @param mixed $second The second sequence.
     * @param callable $selector The selector that produces the result element from two input elements.
     *
     * @return IEnumerable The new sequence.
     */
    function zip($second, $selector);
}
