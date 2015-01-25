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
     * Same as \Iterator::rewind() method.
     */
    function reset();
    
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
     * Skips a specific number of elements.
     * 
     * @param integer $count The number of elements to skip.
     * 
     * @return IEnumerable The new sequence.
     */
    function skip($count);
    
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
     * Returns the items of that sequence as new array.
     * 
     * @return array That sequence as array.
     */
    function toArray();
    
    /**
     * Converts that sequence to an array that is similar to a hashtable
     * or dictionary.
     * 
     * @param callable $keySelector The optional key selector to use.
     * 
     * @return array The hashtable / dictionary.
     */
    function toDictionary($keySelector = null);
    
    /**
     * Filters the elements of that sequence.
     * 
     * @param callable $predicate The filter to use.
     * 
     * @return IEnumerable The new sequence.
     */
    function where($predicate);
}
