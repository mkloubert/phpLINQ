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
 * A basic sequence.
 *
 * @package System\Collections
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
abstract class EnumerableBase implements IEnumerable {
    /**
     * @var \Iterator
     */
    protected $_i;


    /**
     * Initializes a new instance of that class.
     *
     * @param \Iterator $i The inner iterator.
     */
    protected function __construct(\Iterator $i) {
        $this->_i = $i;
    }


    public final function aggregate($accumulator, $defValue = null) {
        return $this->aggregateInner($accumulator, $defValue);
    }

    protected function aggregateInner(callable $accumulator, $defValue) {
        $result = $defValue;

        $index = 0;
        while ($this->valid()) {
            $ctx = static::createContextObject($this, $index++);

            if (!$ctx->isFirst) {
                $result = call_user_func($accumulator,
                                         $result, $ctx->value, $ctx);
            }
            else {
                $result = $ctx->value;
            }
        }

        return $result;
    }

    /**
     * Returns an object / value as iterator.
     *
     * @param mixed $obj The object to convert / cast.
     *
     * @return \Iterator|null
     */
    protected static function asIterator($obj) {
        if (is_null($obj)) {
            return null;
        }

        if ($obj instanceof \Iterator) {
            return $obj;
        }

        $arr = $obj;

        if (!is_array($obj)) {
            $arr = array();

            if (is_string($obj)) {
                // char sequence

                for ($i = 0; $i < strlen($obj); $i++) {
                    $arr[] = $obj[$i];
                }
            }
            else {
                // Traversable

                foreach ($obj as $item) {
                    $arr[] = $item;
                }
            }
        }

        return new \ArrayIterator($arr);
    }

    /**
     * Builds a new sequence by using a factory function.
     *
     * @param int $count The number of items to build.
     * @param callable $itemFactory The function that builds an item.
     *
     * @return static The new sequence.
     */
    public static function build($count, callable $itemFactory) {
        $items = array();

        $index = 0;
        while ($index < $count) {
            $items[] = call_user_func($itemFactory,
                                      $index++);
        }

        return static::create($items);
    }

    public final function concat() {
        return static::create($this->concatInner(func_get_args()));
    }

    /**
     * @see EnumerableBase::concat()
     */
    protected function concatInner(array $itemLists) {
        array_unshift($itemLists, $this);

        foreach ($itemLists as $items) {
            $iterator = static::asIterator($items);
            if (is_null($iterator)) {
                continue;
            }

            while ($iterator->valid()) {
                yield $iterator->current();

                $iterator->next();
            }
        }
    }

    public function concatValues() {
        return $this->concat(func_get_args());
    }

    public function count() {
        if ($this->_i instanceof \Countable) {
            return $this->_i->count();
        }

        $result = 0;
        while ($this->valid()) {
            ++$result;

            $this->next();
        }

        return $result;
    }

    /**
     * Creates a new instance.
     *
     * @param mixed $items The initial items.
     *
     * @return static The new instance.
     */
    public static function create($items = null) {
        if (is_null($items)) {
            $items = new \EmptyIterator();
        }

        return new static(static::asIterator($items));
    }

    /**
     * Creates a basic context object for callables.
     *
     * @param \Iterator $i The underlying iterator.
     * @param null $index The current index.
     * @param bool $invokeNext Invoke \Iterator::next() method or not.
     *
     * @return \stdClass The created object.
     */
    protected static function createContextObject(\Iterator $i, $index = null, $invokeNext = true) {
        $result           = new \stdClass();
        $result->index    = $index;
        $result->isLast   = null;
        $result->iterator = $i;
        $result->key      = $i->key();
        $result->value    = $i->current();

        if (!is_null($result->index)) {
            $result->isFirst = 0 == $result->index;
        }

        if ($invokeNext) {
            $i->next();

            $result->isLast = !$i->valid();
        }

        return $result;
    }

    public function current() {
        return $this->_i->current();
    }

    public final function distinct($equalityComparer = null) {
        return static::create($this->distinctInner($equalityComparer));
    }

    /**
     * @see EnumerableBase::distinct()
     */
    protected function distinctInner(callable $equalityComparer = null) {
        $equalityComparer = static::getEqualComparerSafe($equalityComparer);

        $uniques = array();

        while ($this->valid()) {
            $ci = $this->current();

            // search for duplicate
            $alreadyInList = false;
            foreach ($uniques as $ui) {
                if (call_user_func($equalityComparer,
                                   $ci, $ui)) {
                    // found duplicate

                    $alreadyInList = true;
                    break;
                }
            }

            if (!$alreadyInList) {
                $uniques[] = $ci;
                yield $ci;
            }

            $this->next();
        }
    }

    /**
     * Creates a new instance from JSON data.
     *
     * @param string $json The JSON data.
     *
     * @return static The new instance.
     */
    public static function fromJson($json) {
        return static::create(json_decode($json, true));
    }

    /**
     * Creates a new instance from a list of values.
     *
     * @param mixed $value... The initial values.
     *
     * @return static The new instance.
     */
    public static function fromValues() {
        return static::create(func_get_args());
    }

    /**
     * Keeps sure that a comparer function is NOT (null).
     *
     * @param callable $comparer The input value.
     *
     * @return callable The output value.
     */
    protected static function getComparerSafe(callable $comparer = null) {
        if (is_null($comparer)) {
            $comparer = function($x, $y) {
                if ($x > $y) {
                    return 1;
                }
                else if ($x < $y) {
                    return -1;
                }

                return 0;
            };
        }

        return $comparer;
    }

    /**
     * Keeps sure that a equality comparer is NOT (null).
     *
     * @param callable $equalityComparer The input value.
     *
     * @return callable The output value.
     */
    protected static function getEqualComparerSafe($equalityComparer) {
        if (is_null($equalityComparer)) {
            $equalityComparer = function($x, $y) {
                return $x == $y;
            };
        }

        return $equalityComparer;
    }

    /**
     * Keeps sure that a predicate function is NOT (null).
     *
     * @param callable $predicate The input value.
     *
     * @return callable The output value.
     */
    protected static function getPredicateSafe(callable $predicate = null) {
        if (is_null($predicate)) {
            $predicate = function() {
                return true;
            };
        }

        return $predicate;
    }

    public function isEmpty() {
        return !$this->valid();
    }

    public final function isNotEmpty() {
        return !$this->isEmpty();
    }

    public function key() {
        return $this->_i->key();
    }

    public final function max($defValue = null, $comparer = null) {
        $comparer = static::getComparerSafe($comparer);

        return $this->aggregate(function($result, $item) use ($comparer) {
                                    // check if result item is smaller
                                    // than the current one

                                    return call_user_func($comparer, $result, $item) < 0 ? $item
                                                                                         : $result;
                                }, $defValue);
    }

    public final function min($defValue = null, $comparer = null) {
        $comparer = static::getComparerSafe($comparer);

        return $this->aggregate(function($result, $item) use ($comparer) {
                                    // check if result item is greater
                                    // than the current one

                                    return call_user_func($comparer, $result, $item) > 0 ? $item
                                                                                         : $result;
                                }, $defValue);
    }

    public function next() {
        $this->_i->next();
    }

    public function ofType($type) {
        $type = trim($type);

        return $this->where(function($x) use ($type) {
                                if (empty($type)) {
                                    return is_null($x);
                                }

                                if (is_object($x)) {
                                    if ('object' == $type) {
                                        return true;
                                    }

                                    if (class_exists($type)) {
                                        $reflect = new \ReflectionClass($type);

                                        return $reflect->isInstance($x);
                                    }
                                }

                                return gettype($x) == $type;
                            });
    }

    public final function order($comparer = null) {
        return $this->orderBy(function($x) {
                                  return $x;
                              }, $comparer);
    }

    public function orderBy($selector, $comparer = null) {
        $comparer = static::getComparerSafe($comparer);

        $result = $this->select(function($x, $ctx) use ($selector) {
                                    $result         = new \stdClass();
                                    $result->sortBy = call_user_func($selector,
                                                                      $x, $ctx);
                                    $result->value  = $x;

                                    return $result;
                                })
                       ->toArray();

        usort($result, function($x, $y) use ($comparer) {
            return call_user_func($comparer,
                                  $x->sortBy, $y->sortBy);
        });

        return static::create($result)
                     ->select(function($x) {
                                  return $x->value;
                              });
    }

    public final function orderByDescending($selector, $comparer = null) {
        $comparer = static::getComparerSafe($comparer);

        return $this->orderBy($selector,
                              function($x, $y) use ($comparer) {
                                  return -1 * call_user_func($comparer,
                                                             $x, $y);
                              });
    }

    public final function orderDescending($comparer = null) {
        return $this->orderByDescending(function($x) {
                                            return $x;
                                        }, $comparer);
    }

    public final function product($defValue = null) {
        return $this->aggregate(function($result, $item) {
                                    return $result * $item;
                                }, $defValue);
    }

    public function randomize($seeder = null, $randProvider = null) {
        if (is_null($randProvider)) {
            $randProvider = function () {
                return mt_rand();
            };
        }

        if (!is_null($seeder)) {
            call_user_func($seeder);
        }

        return $this->orderBy($randProvider);
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

    public final function reset() {
        $this->rewind();
        return $this;
    }

    public final function reverse() {
        return $this->orderBy(function($x, $ctx) {
                                  return PHP_INT_MAX - $ctx->index;
                              });
    }

    public function rewind() {
        $this->_i->rewind();
    }

    public final function runtimeVersion() {
        return "5.5";
    }

    public final function select($selector) {
        return static::create($this->selectInner($selector));
    }

    /**
     * @see EnumerableBase::select()
     */
    protected function selectInner(callable $selector) {
        $index = 0;
        while ($this->valid()) {
            $ctx = static::createContextObject($this, $index++);

            yield call_user_func($selector,
                                 $ctx->value, $ctx);
        }
    }

    public final function selectMany($selector) {
        return static::create($this->selectManyInner($selector));
    }

    /**
     * @see EnumerableBase::selectMany()
     */
    protected function selectManyInner(callable $selector) {
        $index = 0;
        while ($this->valid()) {
            $ctx = static::createContextObject($this, $index++);

            $iterator = static::asIterator(call_user_func($selector,
                                                          $ctx->value, $ctx));

            if (is_null($iterator)) {
                continue;
            }

            while ($iterator->valid()) {
                yield $iterator->current();

                $iterator->next();
            }
        }
    }

    public final function sequenceEqual($other, $equalityComparer = null) {
        $equalityComparer = static::getEqualComparerSafe($equalityComparer);

        if (is_null($other)) {
            $other = array();
        }

        $other = static::asIterator($other);

        while ($this->valid()) {
            $x = $this->current();

            if (!$other->valid()) {
                // that sequence has more items
                return false;
            }

            $y = $other->current();

            if (!call_user_func($equalityComparer, $x, $y)) {
                // both items are NOT equal
                return false;
            }
        }

        if ($other->valid()) {
            // other has more items
            return false;
        }

        return true;
    }

    public function serialize() {
        return $this->toJson();
    }

    public final function singleOrDefault($predicate = null, $defValue = null) {
        static::updatePredicateAndDefaultValue(func_num_args(),
                                               $predicate, $defValue);

        $predicate = static::getPredicateSafe($predicate);

        $result = $defValue;

        $index = 0;
        $hasAlreadyBeenFound = false;
        while ($this->valid()) {
            $ctx = static::createContextObject($this, $index++);

            if (call_user_func($predicate, $ctx->value, $ctx)) {
                if ($hasAlreadyBeenFound) {
                    throw new \Exception('Sequence contains more than one matching element!');
                }

                $result              = $ctx->value;
                $hasAlreadyBeenFound = true;
            }
        }

        return $result;
    }

    public final function skip($count) {
        return $this->skipWhile(function($x, $ctx) use ($count) {
                                    return $ctx->index < $count;
                                });
    }

    public final function skipWhile($predicate) {
        return static::create($this->skipWhileInner($predicate));
    }

    /**
     * @see EnumerableBase::skipWhile()
     */
    protected function skipWhileInner(callable $predicate) {
        $index = 0;
        while ($this->valid()) {
            $ctx = static::createContextObject($this, $index++, false);

            if (call_user_func($predicate,
                               $ctx->value, $ctx)) {

                $this->next();
            }
            else {
                break;
            }
        }

        while ($this->valid()) {
            yield $this->current();

            $this->next();
        }
    }

    public final function sum($defValue = null) {
        return $this->aggregate(function($result, $item) {
                                    return $result + $item;
                                }, $defValue);
    }

    public final function take($count) {
        return $this->takeWhile(function($x, $ctx) use ($count) {
                                    return $ctx->index < $count;
                                });
    }

    public final function takeWhile($predicate) {
        return static::create($this->takeWhileInner($predicate));
    }

    /**
     * @see EnumerableBase::takeWhile()
     */
    protected function takeWhileInner(callable $predicate) {
        $index = 0;
        while ($this->valid()) {
            $ctx = static::createContextObject($this, $index++, false);

            if (call_user_func($predicate,
                               $ctx->value, $ctx)) {

                yield $ctx->value;
                $this->next();
            }
            else {
                break;
            }
        }
    }

    public function toArray($keySelector = null) {
        if (is_null($keySelector)) {
            $keySelector = function() {
                return null;
            };
        }

        $result = array();

        $index = 0;
        while ($this->valid()) {
            $ctx = static::createContextObject($this, $index++);

            $key = call_user_func($keySelector,
                                  $ctx->key, $ctx->value, $ctx);

            if (is_null($key)) {
                // autokey
                $result[] = $ctx->value;
            }
            else {
                $result[$key] = $ctx->value;
            }
        }

        return $result;
    }

    public function toJson($keySelector = null, $options = null) {
        if (func_num_args() == 1) {
            if (!is_null($keySelector) && !is_callable($keySelector)) {
                // swap values

                $options     = $keySelector;
                $keySelector = null;
            }
        }

        if (is_null($keySelector)) {
            $keySelector = function($key) {
                return $key;
            };
        }

        if (is_null($options)) {
            $options = 0;
        }

        return json_encode($this->toArray($keySelector),
                           (int)$options);
    }

    public function union($second, $equalityComparer = null) {
        return $this->concat($second)
                    ->distinct($equalityComparer);
    }

    /**
     * Updates the variables $predicate and $defValue by the submitted arguments of a method / function.
     *
     * @param int $argCount The number of submitted arguments.
     * @param mixed $predicate The predicate.
     *                         If there is only one submitted argument and this variable contains
     *                         no callable, it is set to (null) and its origin value is written to $defValue.
     * @param mixed $defValue The value that contains the default value.
     */
    protected static function updatePredicateAndDefaultValue($argCount, &$predicate, &$defValue) {
        if (1 == $argCount) {
            if (!is_callable($predicate)) {
                // use $predicate as default value

                $defValue  = $predicate;
                $predicate = null;
            }
        }
    }

    public function unserialize($serialized) {
        $temp = static::fromJson($serialized);

        $this->__construct($temp->_i);
        unset($temp);
    }

    public function valid() {
        return $this->_i->valid();
    }

    public final function where($predicate) {
        return static::create($this->whereInner($predicate));
    }

    /**
     * @see EnumerableBase::where()
     */
    protected function whereInner(callable $predicate) {
        $index = 0;
        while ($this->valid()) {
            $ctx = static::createContextObject($this, $index++);

            if (call_user_func($predicate,
                               $ctx->value, $ctx)) {

                yield $ctx->value;
            }
        }
    }

    public final function zip($second, $selector) {
        return static::create($this->zipInner($second, $selector));
    }

    /**
     * @see EnumerableBase::zip()
     */
    protected function zipInner($second, callable $selector) {
        $second = static::asIterator($second);
        if (is_null($second)) {
            $second = new \EmptyIterator();
        }

        $index = 0;
        while ($this->valid() && $second->valid()) {
            $ctx1 = static::createContextObject($this, $index);
            $ctx2 = static::createContextObject($second, $index);
            ++$index;

            yield call_user_func($selector,
                                 $ctx1->value, $ctx2->value, $ctx1, $ctx2);
        }
    }
}
