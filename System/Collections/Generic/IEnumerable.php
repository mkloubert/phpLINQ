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


namespace System\Collections\Generic;

use \System\Collections\IDictionary;
use \System\Collections\IList;
use \System\Linq\ILookup;


/**
 * Describes a sequence.
 * 
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
interface IEnumerable extends \Iterator, \Countable {
    /**
     * Checks if at all elements
     * match a predicate.
     *
     * @param callable $predicate The predicate to use.
     * 
     * @return boolean All elements match predicate.
     */
    function all($predicate);
    
    /**
     * Checks if at least one element exists that
     * matches a predicate.
     * 
     * @param callable $predicate The optional predicate to use.
     * 
     * @return boolean At least one element matches predicate.
     */
    function any($predicate = null);
    
    /**
     * Calculates the average value of all elements of that sequence.
     * 
     * @param mixed $defValue The value to return if no element was found.
     * 
     * @return number The average value.
     */
    function average($defValue = null);
    
    /**
     * Casts each element to a target type.
     * 
     * @param string $type The name of the target type.
     * 
     * @return IEnumerable The new sequence.
     */
    function cast($type);
    
    /**
     * Concats an iterator / array with that sequence.
     * 
     * @param array|\Traversable $iterator The iterator / array to concat.
     * 
     * @return IEnumerable The concated sequences / lists.
     */
    function concat($iterator);
    
    /**
     * Checks if that sequence contains a specific element.
     * 
     * @param mixed $item The element to search for.
     * @param callable $comparer The optional comparer function.
     * 
     * @return boolean Element exists or not.
     */
    function contains($item, $comparer = null);
    
    /**
     * Returns the elements of the sequence or a sequence with default values if
     * that sequence is empty.
     * 
     * @return IEnumerable The new sequence.
     */
    function defaultIfEmpty();
    
    /**
     * Returns a distincted sequences.
     * 
     * @param callable $comparer The optional comparer function.
     * 
     * @return IEnumerable The new sequence.
     */
    function distinct($comparer = null);
    
    /**
     * Returns an element at a specific position or a default
     * value if not found.
     * 
     * @param Integer $index The zero-based index.
     * @param mixed $defValue The value to return if element was not found.
     * 
     * @return mixed The value.
     */
    function elementAtOrDefault($index, $defValue = null);
    
    /**
     * Produces the difference between that sequence and another.
     * 
     * @param Traversable|array $second The items to exclude.
     * @param callable $comparer The optional, custom item comparer to use.
     * 
     * @return IEnumerable The new sequence.
     */
    function except($second, $comparer = null);
    
    /**
     * Returns the first item of that sequence.
     * 
     * @param callable|mixed $predicate The optional predicate to use.
     *                                  If there is only one argument and that
     *                                  value is NOT callable it will be used
     *                                  as default value.
     * @param mixed $defValue The default value if no item was found.
     * 
     * @return mixed The first or default value.
     */
    function firstOrDefault($predicate = null, $defValue = null);
    
    /**
     * Groups the sequence.
     * 
     * @param callable $keySelector The function that provides the key / group value
     *                              for the current element.
     * @param callable $keyComparer The optional key comparer to use.
     *                              
     * @return IEnumerable The sequence of groups (@see IGrouping).
     */
    function groupBy($keySelector, $keyComparer = null);
    
    /**
     * Correlates the elements of that sequence and another
     * based on matching keys and groups them.
     *
     * @param Traversable|array $inner The other sequence.
     * @param callable $outerKeySelector The key selector for the items of
     *                                   that sequence.
     * @param callable $innerKeySelector The key selector for the items of
     *                                   the other sequence.
     * @param callable $resultSelector The function that provides the result
     *                                 value for two matching elements.
     * @param callable $keyComparer The optional custom key function for
     *                              comparing the keys of the two sequences.
     *
     * @return IEnumerable The new sequence.
     */
    function groupJoin($inner,
                       $outerKeySelector, $innerKeySelector,
                       $resultSelector,
                       $keyComparer = null);
    
    /**
     * Produces the set intersection of that sequence and another.
     *
     * @param Traversable|array $second The other items.
     * @param callable $comparer The optional, custom item comparer to use.
     *
     * @return IEnumerable The new sequence.
     */
    function intersect($second, $comparer = null);
    
    /**
     * Correlates the elements of that sequence and another
     * based on matching keys.
     * 
     * @param Traversable|array $inner The other sequence.
     * @param callable $outerKeySelector The key selector for the items of
     *                                   that sequence.
     * @param callable $innerKeySelector The key selector for the items of
     *                                   the other sequence.
     * @param callable $resultSelector The function that provides the result
     *                                 value for two matching elements.
     * @param callable $keyComparer The optional custom key function for
     *                              comparing the keys of the two sequences.
     *                              
     * @return IEnumerable The new sequence.
     */
    function join($inner,
                  $outerKeySelector, $innerKeySelector,
                  $resultSelector,
                  $keyComparer = null);
    
    /**
     * Returns the last item of that sequence.
     *
     * @param callable|mixed $predicate The optional predicate to use.
     *                                  If there is only one argument and that
     *                                  value is NOT callable it will be used
     *                                  as default value.
     * @param mixed $defValue The default value if no item was found.
     *
     * @return mixed The last or default value.
     */
    function lastOrDefault($predicate = null, $defValue = null);
    
    /**
     * Gets the maximum value.
     * 
     * @param mixed $defValue Use that value if sequence is empty.
     * 
     * @return number The maximum value.
     */
    function max($defValue = null);
    
    /**
     * Gets the minimum value.
     *
     * @param mixed $defValue Use that value if sequence is empty.
     *
     * @return number The minimum value.
     */
    function min($defValue = null);
    
    /**
     * Multiplies all elements of that sequence.
     *
     * @param mixed $defValue Use that value if sequence is empty.
     *
     * @return number The result of the multiplication.
     */
    function multiply($defValue = null);

    /**
     * Selects all elements of a specific type.
     * 
     * @param string $type The name of the type to filter.
     * 
     * @return IEnumerable The filtered sequence.
     */
    function ofType($type);
    
    /**
     * Orders that sequence by using the items as sort values.
     *
     * @param callable $algo The optional, custom algorithm to use.
     *
     * @return IEnumerable The ordered sequence.
     */
    function order($algo = null);
    
    /**
     * Orders that sequence.
     * 
     * @param callable $sortSelector The function that provides the sort value.
     * @param callable $algo The optional, custom algorithm to use.
     * 
     * @return IEnumerable The ordered sequence.
     */
    function orderBy($sortSelector, $algo = null);
    
    /**
     * Orders that sequence (descending).
     *
     * @param callable $sortSelector The function that provides the sort value.
     * @param callable $algo The optional, custom algorithm to use.
     *
     * @return IEnumerable The ordered sequence.
     */
    function orderByDescending($sortSelector, $algo = null);
    
    /**
     * Orders that sequence (descending) by using the items as sort values.
     *
     * @param callable $algo The optional, custom algorithm to use.
     *
     * @return IEnumerable The ordered sequence.
     */
    function orderDescending($algo = null);
    
    /**
     * Alias of @see \System\Collections\Generic\IEnumerable::multiply()
     */
    function product($defValue = null);
    
    /**
     * Randomizes the order of the sequence.
     * 
     * @param number|callable $seed The seed value / function.
     * @param callable $randomizer The function that provides the random
     *                             sort / order value.
     * 
     * @return IEnumerable The new sequence.
     */
    function randomize($seed = null, $randomizer = null);
    
    /**
     * Same as \Iterator::rewind() method.
     * 
     * @return IEnumerable The current sequence.
     */
    function reset();
    
    /**
     * Reverses the order of the sequence.
     * 
     * @return IEnumerable The new sequence.
     */
    function reverse();
    
    /**
     * Selects each item of that sequence to a new type.
     * 
     * @param callable $selector The selector.
     * 
     * @return IEnumerable The new sequence.
     */
    function select($selector);
    
    /**
     * Projects each element of a sequence to sequence and flattens
     * the resulting sequences into one sequence.
     * 
     * @param callable $selector The selector.
     * 
     * @return IEnumerable The new / flatten sequence.
     */
    function selectMany($selector);
    
    /**
     * Checks if the items of that sequence or all equal with the items of
     * another one.
     * 
     * @param Traversable|array $second The other sequence.
     * @param callable $comparer The optional custom item comparer to use.
     * 
     * @return boolean Are equal or not.
     */
    function sequenceEqual($second, $comparer = null);
    
    /**
     * Returns the one and only item of that sequence.
     * An exception is thrown if there is more than one element.
     *
     * @param callable|mixed $predicate The optional predicate to use.
     *                                  If there is only one argument and that
     *                                  value is NOT callable it will be used
     *                                  as default value.
     * @param mixed $defValue The default value if no item was found.
     *
     * @return mixed The item or default value.
     * 
     * @throws \Exception More than one element found.
     */
    function singleOrDefault($predicate = null, $defValue = null);
    
    /**
     * Skips a specific number of elements.
     * 
     * @param integer $count The number of elements to skip.
     * 
     * @return IEnumerable The new sequence.
     */
    function skip($count);
    
    /**
     * Skips elements while a predicate matches.
     * 
     * @param callable $predicate The predicate to use.
     * 
     * @return IEnumerable The new sequence.
     */
    function skipWhile($predicate);
    
    /**
     * Concats the elements of that sequence to one string.
     *
     * @param callable $selector The custom string provider for an element.
     *
     * @return string The generated string.
     */
    function stringConcat($selector = null);
    
    /**
     * Joins the elements of that sequence to one string.
     * 
     * @param string $separator The string expression between two elements.
     * @param callable $selector The custom string provider for an element.
     * 
     * @return string The generated string.
     */
    function stringJoin($separator, $selector = null);
    
    /**
     * Calculates the sum of the elements of that sequence.
     * 
     * @param mixed The value that is returned if no element was found.
     * 
     * @return number The sum of the elements.
     */
    function sum($defValue = null);
    
    /**
     * Takes a specific number of elements.
     *
     * @param integer $count The number of elements to take.
     * 
     * @return IEnumerable The new sequence.
     */
    function take($count);
    
    /**
     * Takes elements while a predicate matches.
     *
     * @param callable $predicate The predicate to use.
     *
     * @return IEnumerable The new sequence.
     */
    function takeWhile($predicate);
    
    /**
     * Returns the items of that sequence as new array.
     * 
     * @param callable $keySelector The optional key selector to use.
     * 
     * @return array That sequence as array.
     */
    function toArray($keySelector = null);
    
    /**
     * Converts that sequence to a new dictionary.
     * 
     * @param callable $keySelector The optional key selector to use.
     * @param callable $keyComparer The optional key comparer to use.
     * 
     * @return IDictionary The hashtable / dictionary.
     */
    function toDictionary($keySelector = null, $keyComparer = null);
    
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
     * @param callable $keyComparer The custom key comparer to use.
     * @param callable $elementSelector The custom element selector to use. 
     * 
     * @return ILookup The sequence as lookup.
     */
    function toLookup($keySelector = null, $keyComparer = null,
                      $elementSelector = null);
    
    /**
     * Returns a new set of that sequence.
     * 
     * @param callable $comparer The custom item comparer to use.
     * 
     * @return ISet The new set.
     */
    function toSet($comparer = null);
    
    /**
     * Produces the set union of that sequence and another.
     *
     * @param Traversable|array $second The other items.
     * @param callable $comparer The optional, custom item comparer to use.
     *
     * @return IEnumerable The new sequence.
     */
    function union($second, $comparer = null);
    
    /**
     * Filters the elements of that sequence.
     * 
     * @param callable $predicate The filter to use.
     * 
     * @return IEnumerable The new sequence.
     */
    function where($predicate);
    
    /**
     * Applies a specified function to the corresponding elements
     * of that sequence and another, producing a sequence of the results.
     * 
     * @param Traversable|array $second The other items.
     * @param callable $selector The selector function.
     * 
     * @return IEnumerable The new sequence.
     */
    function zip($second, $selector);
}
