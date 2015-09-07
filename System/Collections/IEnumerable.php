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


use \System\Linq\ILookup;


/**
 * Describes a sequence.
 *
 * @package System\Collections
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
interface IEnumerable extends \Countable, \Iterator, \Serializable, \System\IObject {
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
     * Checks if all items match a condition.
     * An empty sequence will return (true).
     *
     * @param callable $predicate The predicate to use.
     *
     * @return bool All items match condition.
     */
    function all($predicate);

    /**
     * Checks if there is at least one element that matches a condition.
     *
     * @param callable $predicate The predicate to use. If (null) at least one element must exist to return (true).
     *
     * @return bool One element found.
     */
    function any($predicate = null);

    /**
     * Appends the items of that sequence to an array.
     *
     * @param array $arr The target array.
     * @param bool $withKeys Also apply keys or not.
     *
     * @return $this
     */
    function appendToArray(array &$arr, $withKeys = false);

    /**
     * Calculates the average value of all values of that sequence.
     *
     * @param mixed $defValue The default value if sequence is empty.
     *
     * @return mixed The average value or the default.
     */
    function average($defValue = null);

    /**
     * Casts the items of that sequence.
     *
     * @param string $type The name of the target type.
     *                     (http://php.net/manual/en/language.types.type-juggling.php)
     *
     * @return IEnumerable The casted sequence.
     */
    function cast($type);

    /**
     * Concats the items of that sequence with one or more others.
     *
     * @param mixed $items... The items to append.
     *
     * @return IEnumerable The new sequence.
     */
    function concat();

    /**
     * Joins all elements of that sequence to one string.
     *
     * @param string $defValue The value to return if sequence is empty.
     *
     * @return string The generated string.
     */
    function concatToString($defValue = '');

    /**
     * Concats the items of that sequence with a list of values.
     *
     * @param mixed $items... The items to append.
     *
     * @return IEnumerable The new sequence.
     */
    function concatValues();

    /**
     * Checks if an item exists in that sequence.
     *
     * @param mixed $item The item to check.
     * @param callable $equalityComparer The custom equality comparer to use.
     *
     * @return bool Sequence contains item or not.
     */
    function contains($item, $equalityComparer = null);

    /**
     * Returns a default sequence if that sequence is empty.
     *
     * @param mixed $item... The items for the default sequence.
     *
     * @return IEnumerable The default instance or that sequence if it is not empty.
     */
    function defaultIfEmpty();

    /**
     * Returns a default sequence if that sequence is empty.
     *
     * @param mixed $items The items for the default sequence.
     *
     * @return IEnumerable The default instance or that sequence if it is not empty.
     */
    function defaultIfEmpty2($items);

    /**
     * Removes duplicates.
     *
     * @param callable|null $equalityComparer The custom equality comparer.
     *
     * @return IEnumerable The new sequence.
     */
    function distinct($equalityComparer = null);

    /**
     * Iterates over that sequence by using a callable.
     *
     * @param callable $action The action to invoke for each item.
     * @param mixed $defResult The initial / default result.
     *
     * @return mixed The current result value from the iteration.
     */
    function each($action, $defResult = null);

    /**
     * Returns an element at a specific position.
     *
     * @param int $index The zero based index.
     * @param mixed $defValue The value to return if element was not found.
     *
     * @return mixed The element or the default value.
     */
    function elementAtOrDefault($index, $defValue = null);

    /**
     * Returns the items of that sequence except the items of other one.
     *
     * @param mixed $second The other sequence.
     * @param callable $equalityComparer The custom equaler function.
     *
     * @return IEnumerable The new sequence.
     */
    function except($second, $equalityComparer = null);

    /**
     * Returns the first matching value of that sequence or a default value if not found.
     *
     * @param mixed $predicateOrDefValue The custom predicate to use.
     *                                   If there is only one submitted argument and this variable contains
     *                                   no callable, it is set to (null) and its origin value is written to $defValue.
     * @param mixed $defValue The default value to return if value was not found.
     *
     * @return mixed The first matching value or the default value.
     */
    function firstOrDefault($predicateOrDefValue = null, $defValue = null);

    /**
     * Returns a formatted string based on the items of that sequence.
     *
     * @param string $format The format string.
     *                       The format is similar to the Format Item Syntax (Index Component)
     *                       of the .NET framework
     *                       (https://msdn.microsoft.com/en-us/library/txafckwd%28v=vs.110%29.aspx).
     *
     * @return string The formatted string.
     */
    function format($format);

    /**
     * Groups the items of that sequence.
     *
     * @param callable $keySelector The key selector.
     * @param callable $keyEqualityComparer The custom equality function for the keys.
     *
     * @return IEnumerable The grouped items as a sequence of IGrouping items.
     */
    function groupBy($keySelector, $keyEqualityComparer = null);

    /**
     * Correlates the elements of that sequence and another based on matching keys and groups items.
     *
     * @param mixed $inner The other sequence.
     * @param callable $outerKeySelector The key selector for the items of that sequence.
     * @param callable $innerKeySelector The key selector for the items of the other sequence.
     * @param callable $resultSelector The function that provides the result value for two matching elements.
     * @param callable $keyEqualityComparer The custom equality function for the keys.
     *
     * @return static The joined sequence.
     */
    function groupJoin($inner,
                       $outerKeySelector, $innerKeySelector,
                       $resultSelector,
                       $keyEqualityComparer = null);

    /**
     * Returns the intersection of this sequence and another.
     *
     * @param mixed $second The second sequence.
     * @param callable $equalityComparer The custom equaler function.
     *
     * @return IEnumerable The new sequence.
     */
    function intersect($second, $equalityComparer = null);

    /**
     * Gets if that sequence does not contain items anymore.
     *
     * @return bool Is empty or not.
     */
    function isEmpty();

    /**
     * Gets if that sequence still contains items or not.
     *
     * @return bool Is empty (false) or not (true).
     */
    function isNotEmpty();

    /**
     * Correlates the elements of that sequence and another based on matching keys.
     *
     * @param mixed $inner The other sequence.
     * @param callable $outerKeySelector The key selector for the items of that sequence.
     * @param callable $innerKeySelector The key selector for the items of the other sequence.
     * @param callable $resultSelector The function that provides the result value for two matching elements.
     * @param callable $keyEqualityComparer The custom equality function for the keys.
     *
     * @return IEnumerable The joined sequence.
     */
    function join($inner,
                  $outerKeySelector, $innerKeySelector,
                  $resultSelector,
                  $keyEqualityComparer = null);

    /**
     * Joins all elements of that sequence to one string.
     *
     * @param string $separator The separator to use.
     * @param string $defValue The value to return if sequence is empty.
     *
     * @return string The generated string.
     */
    function joinToString($separator, $defValue = '');

    /**
     * Returns the last matching value of that sequence or a default value if not found.
     *
     * @param mixed $predicateOrDefValue The custom predicate to use.
     *                                   If there is only one submitted argument and this variable contains
     *                                   no callable, it is set to (null) and its origin value is written to $defValue.
     * @param mixed $defValue The default value to return if value was not found.
     *
     * @return mixed The last matching value or the default value.
     */
    function lastOrDefault($predicateOrDefValue = null, $defValue = null);

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
     * @return mixed The minimum value.
     */
    function min($defValue = null, $comparer = null);

    /**
     * Filters all items of a specific type.
     *
     * @param string $type The name of the type.
     *
     * @return IEnumerable The new sequence.
     */
    function ofType($type);

    /**
     * Orders the items of that sequence ascending by using the items as sort value.
     *
     * @param callable $comparer The custom comparer to use.
     *
     * @return IEnumerable The new sequence.
     */
    function order($comparer = null);

    /**
     * Orders the items of that sequence ascending by using a specific sort value.
     *
     * @param callable $selector The selector for the sort values.
     * @param callable $comparer The custom comparer to use.
     *
     * @return IEnumerable The new sequence.
     */
    function orderBy($selector, $comparer = null);

    /**
     * Orders the items of that sequence descending by using a specific sort value.
     *
     * @param callable $selector The selector for the sort values.
     * @param callable $comparer The custom comparer to use.
     *
     * @return IEnumerable The new sequence.
     */
    function orderByDescending($selector, $comparer = null);

    /**
     * Orders the items of that sequence descending by using the items as sort value.
     *
     * @param callable $comparer The custom comparer to use.
     *
     * @return IEnumerable The new sequence.
     */
    function orderDescending($comparer = null);

    /**
     * Calculates the product of the items.
     *
     * @param mixed $defValue The default value if sequence is empty.
     *
     * @return mixed The product of the items.
     */
    function product($defValue = null);

    /**
     * Randomizes the order of that sequence.
     *
     * @param callable $seeder The custom function that initializes the random number generator.
     * @param callable $randProvider The custom function that provides the random values.
     *
     * @return IEnumerable The new sequence.
     */
    function randomize($seeder = null, $randProvider = null);

    /**
     * Extension of \Iterator::rewind() that returns the sequence itself after operation.
     *
     * @return $this
     */
    function reset();

    /**
     * Returns the items of that sequence in reverse order.
     *
     * @return IEnumerable The new sequence.
     */
    function reverse();

    /**
     * Gets the runtime version the sequence is designed for.
     *
     * @return string The runtime version.
     */
    function runtimeVersion();

    /**
     * Projects each element of that sequence to a new sequence.
     *
     * @param callable $selector The selector to use.
     *
     * @return IEnumerable The new sequence.
     */
    function select($selector);

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
     * Checks if another sequence has the same elements as that sequence.
     *
     * @param mixed $other The other sequence.
     * @param callable $equalityComparer The custom equality comparer to use.
     *
     * @return bool Both are equal or not.
     */
    function sequenceEqual($other, $equalityComparer = null);

    /**
     * Returns the one and only matching element in that sequence.
     *
     * @param mixed $predicateOrDefValue The custom predicate to use.
     *                                   If there is only one submitted argument and this variable contains
     *                                   no callable, it is set to (null) and its origin value is written to $defValue.
     * @param mixed $defValue The default value if element was not found.
     *
     * @return mixed The found element or the default value.
     *
     * @throws \Exception Sequence contains more than one element.
     */
    function singleOrDefault($predicateOrDefValue = null, $defValue = null);

    /**
     * Skip a specific number of items in that sequence.
     *
     * @param int $count The number of items to skip.
     *
     * @return $this
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
     * Converts that sequence to a new dictionary.
     *
     * @param callable $keySelector The custom key selector to use.
     * @param callable $keyEqualityComparer The custom key equality comparer to use.
     *
     * @return IDictionary The hashtable / dictionary.
     */
    function toDictionary($keySelector = null, $keyEqualityComparer = null);

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
     * Converts the sequence to a new list.
     *
     * @return IList The new list.
     */
    function toList();

    /**
     * Converts that sequence to a new lookup object.
     *
     * @param callable $keySelector The custom key selector to use.
     * @param callable $keyEqualityComparer The custom key comparer to use.
     * @param callable $elementSelector The custom element selector to use.
     *
     * @return ILookup The sequence as lookup.
     */
    function toLookup($keySelector = null, $keyEqualityComparer = null,
                      $elementSelector = null);

    /**
     * Returns a new set of that sequence.
     *
     * @param callable $equalityComparer The custom item comparer to use.
     *
     * @return ISet The new set.
     */
    function toSet($equalityComparer = null);

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
     * @param callable $predicate The filter predicate to use.
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
