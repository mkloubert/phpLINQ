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

use \System\ClrString;
use \System\IComparable;
use \System\IObject;
use \System\Linq\Grouping;
use \System\Linq\IGrouping;
use \System\Linq\Lookup;


/**
 * A basic sequence.
 *
 * @package System\Collections
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
abstract class EnumerableBase extends \System\Object implements IEnumerable {
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


    /**
     * {@inheritDoc}
     */
    public final function aggregate($accumulator, $defValue = null) {
        $result = $defValue;

        $index = 0;
        while ($this->valid()) {
            $ctx = static::createContextObject($this, $index++);

            if (!$ctx->isFirst) {
                $result = \call_user_func($accumulator,
                                          $result, $ctx->value, $ctx);
            }
            else {
                $result = $ctx->value;
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public final function all($predicate) {
        $index = 0;
        while ($this->valid()) {
            $ctx = static::createContextObject($this, $index++);

            if (!\call_user_func($predicate, $ctx->value, $ctx)) {
                return false;
            }
        }

        // no item found that does not match
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public final function any($predicate = null) {
        $predicate = static::getPredicateSafe($predicate);

        $index = 0;
        while ($this->valid()) {
            $ctx = static::createContextObject($this, $index++);

            if (\call_user_func($predicate, $ctx->value, $ctx)) {
                return true;
            }
        }

        // no matching item found
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function appendToArray(array &$arr, $withKeys = false) {
        while ($this->valid()) {
            $item = $this->current();

            if (!$withKeys) {
                $arr[] = $item;
            }
            else {
                $arr[$this->key()] = $item;
            }

            $this->next();
        }

        return $this;
    }

    /**
     * Returns an object / value as iterator.
     *
     * @param mixed $obj The object to convert / cast.
     * @param bool $emptyIfNull Return empty iterator if $obj is (null) or return (null).
     *
     * @return \Iterator|null $obj as iterator or (null) if $obj is also (null).
     */
    public static function asIterator($obj, $emptyIfNull = false) {
        while (null !== $obj) {
            if ($obj instanceof \IteratorAggregate) {
                $obj = $obj->getIterator();
                continue;
            }

            break;
        }

        if (null === $obj) {
            if (!$emptyIfNull) {
                return null;
            }
            else {
                $obj = new \EmptyIterator();
            }
        }

        if ($obj instanceof \Iterator) {
            // nothing to convert
            return $obj;
        }

        $arr = $obj;

        if (!\is_array($arr)) {
            $arr = array();

            if (\is_string($obj)) {
                // char sequence

                $len = \strlen($obj);
                for ($i = 0; $i < $len; $i++) {
                    $arr[] = $obj[$i];
                }
            }
            else {
                // Traversable

                $arr = \iterator_to_array($obj);
            }
        }

        return new \ArrayIterator($arr);
    }

    /**
     * {@inheritDoc}
     */
    public final function average($defValue = null) {
        $count = 0;
        $sum = $this->each(function($x, $ctx) use (&$count) {
                               $count = $ctx->index + 1;

                               $ctx->result = !$ctx->isFirst ? $ctx->result + $x
                                                             : $x;
                           });

        return $count > 0 ? \floatval($sum) / \floatval($count)
                          : $defValue;
    }

    /**
     * {@inheritDoc}
     */
    public final function cast($type) {
        $code = \sprintf('return (%s)$x;', \trim($type));

        return $this->select(function($x) use ($code) {
                                 return eval($code);
                             });
    }

    /**
     * {@inheritDoc}
     */
    public final function concat() {
        return static::createEnumerable($this->concatInner(\func_get_args()));
    }

    /**
     * @see EnumerableBase::concat()
     */
    protected function concatInner(array $itemLists) {
        $result = $this->toArray();

        foreach ($itemLists as $items) {
            $iterator = static::asIterator($items, true);

            while ($iterator->valid()) {
                $result[] = $iterator->current();

                $iterator->next();
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public final function concatToString($defValue = '') {
        return ClrString::concat($this, $defValue);
    }

    /**
     * {@inheritDoc}
     */
    public function concatValues() {
        return $this->concat(\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public final function contains($item, $equalityComparer = null) {
        $equalityComparer = static::getEqualComparerSafe($equalityComparer);

        return $this->any(function($x) use ($item, $equalityComparer) {
                              return \call_user_func($equalityComparer,
                                                     $x, $item);
                          });
    }

    /**
     * {@inheritDoc}
     */
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
        $result->isFirst  = null;
        $result->isLast   = null;
        $result->iterator = $i;
        $result->key      = $i->key();
        $result->value    = $i->current();

        if (null !== $result->index) {
            $result->isFirst = 0 == $result->index;
        }

        if ($invokeNext) {
            $i->next();

            $result->isLast = !$i->valid();
        }

        return $result;
    }

    /**
     * Creates a new sequence.
     *
     * @param mixed $items The items of the sequence.
     *
     * @return IEnumerable The new sequence.
     */
    public static function createEnumerable($items = null) {
        return new static(static::asIterator($items, true));
    }

    /**
     * {@inheritDoc}
     */
    public function current() {
        return $this->_i->current();
    }

    /**
     * {@inheritDoc}
     */
    public final function defaultIfEmpty() {
        return \call_user_func(array($this, 'defaultIfEmpty2'),
                               \func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public final function defaultIfEmpty2($items) {
        if ($this->isEmpty()) {
            return static::createEnumerable($items);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public final function distinct($equalityComparer = null) {
        return static::createEnumerable($this->distinctInner($equalityComparer));
    }

    /**
     * @see EnumerableBase::distinct()
     */
    protected function distinctInner($equalityComparer) {
        $equalityComparer = static::getEqualComparerSafe($equalityComparer);

        $result = array();

        while ($this->valid()) {
            $ci = $this->current();

            // search for duplicate
            $alreadyInList = false;
            foreach ($result as $ri) {
                if (\call_user_func($equalityComparer,
                                    $ci, $ri)) {
                    // found duplicate

                    $alreadyInList = true;
                    break;
                }
            }

            if (!$alreadyInList) {
                $result[] = $ci;
            }

            $this->next();
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public final function each($action, $defResult = null) {
        $result = $defResult;

        $index   = 0;
        $prevVal = null;
        $tag     = null;
        while ($this->valid()) {
            $ctx          = static::createContextObject($this, $index++);
            $ctx->cancel  = false;
            $ctx->nextVal = null;
            $ctx->prevVal = $prevVal;
            $ctx->result  = $result;
            $ctx->tag     = $tag;

            \call_user_func($action,
                            $ctx->value, $ctx);

            $result = $ctx->result;

            if ($ctx->cancel) {
                break;
            }

            $prevVal = $ctx->nextVal;
            $tag     = $ctx->tag;
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function elementAtOrDefault($index, $defValue = null) {
        return $this->skip($index)
                    ->firstOrDefault(null, $defValue);
    }

    /**
     * {@inheritDoc}
     */
    public final function except($second, $equalityComparer = null) {
        return static::createEnumerable($this->exceptInner($second, $equalityComparer));
    }

    /**
     * @see EnumerableBase::except()
     */
    protected function exceptInner($second, $equalityComparer = null) {
        if (null === $second) {
            $second = array();
        }

        if (!\is_array($second)) {
            $second = \iterator_to_array($second);
        }

        $equalityComparer = static::getEqualComparerSafe($equalityComparer);

        $itemsToExclude = static::createEnumerable($second)
                                ->distinct($equalityComparer)
                                ->toArray();

        $result = array();

        while ($this->valid()) {
            $curItem = $this->current();

            $found = false;
            foreach ($itemsToExclude as $ite) {
                if (\call_user_func($equalityComparer, $ite, $curItem)) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $result[] = $curItem;
            }

            $this->next();
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public final function firstOrDefault($predicateOrDefValue = null, $defValue = null) {
        static::updatePredicateAndDefaultValue(\func_num_args(),
                                               $predicateOrDefValue, $defValue);

        $predicateOrDefValue = static::getPredicateSafe($predicateOrDefValue);

        $index = 0;
        while ($this->valid()) {
            $ctx = static::createContextObject($this, $index++);

            if (\call_user_func($predicateOrDefValue, $ctx->value, $ctx)) {
                return $ctx->value;
            }
        }

        return $defValue;
    }

    /**
     * {@inheritDoc}
     */
    public function format($format) {
        return ClrString::formatArray($format,
                                      $this);
    }

    /**
     * Keeps sure that a comparer function is NOT (null).
     *
     * @param callable $comparer The input value.
     *
     * @return callable The output value.
     */
    protected static function getComparerSafe($comparer) {
        if (null === $comparer) {
            $comparer = function($x, $y) {
                if ($x instanceof IObject) {
                    if ($x instanceof IComparable) {
                        return $x->compareTo($y);
                    }

                    if ($x->equals($y)) {
                        return 0;
                    }
                }

                if ($y instanceof IObject) {
                    if ($y instanceof IComparable) {
                        return $y->compareTo($x) * -1;
                    }

                    if ($y->equals($x)) {
                        return 0;
                    }
                }

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
        $defaultEqualityComparer = function($x, $y) {
            if ($x instanceof IObject) {
                return $x->equals($y);
            }
            else if ($y instanceof IObject) {
                return $y->equals($x);
            }

            return $x == $y;
        };

        if (null === $equalityComparer) {
            $equalityComparer = $defaultEqualityComparer;
        }

        $rf = new \ReflectionFunction($equalityComparer);
        if ($rf->getNumberOfParameters() < 2) {
            // use function as selector

            $equalityComparer = function($x, $y) use ($defaultEqualityComparer, $rf) {
                return \call_user_func($defaultEqualityComparer,
                                       $rf->invoke($x), $rf->invoke($y));
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
    protected static function getPredicateSafe($predicate) {
        if (null === $predicate) {
            $predicate = function() {
                return true;
            };
        }

        return $predicate;
    }

    /**
     * {@inheritDoc}
     */
    public final function groupBy($keySelector, $keyEqualityComparer = null) {
        return $this->groupByInner($keySelector, $keyEqualityComparer);
    }

    /**
     * @see EnumerableBase::groupBy()
     */
    protected function groupByInner($keySelector, $keyEqualityComparer = null) {
        $keyEqualityComparer = static::getEqualComparerSafe($keyEqualityComparer);

        $groups = array();

        $index = 0;
        while ($this->valid()) {
            $ctx = static::createContextObject($this, $index++);

            $key = \call_user_func($keySelector,
                                   $ctx->value, $ctx);

            $grp = null;
            foreach ($groups as $g) {
                if (\call_user_func($keyEqualityComparer, $g->key, $key)) {
                    $grp = $g;
                    break;
                }
            }

            if (null === $grp) {
                $grp         = new \stdClass();
                $grp->key    = $key;
                $grp->values = array();

                $groups[] = $grp;
            }

            $grp->values[] = $ctx->value;
        }

        $cls = new \ReflectionObject($this);

        return static::createEnumerable($groups)
                     ->select(function($x) use ($cls) {
                                  return new Grouping($x->key,
                                                      \call_user_func(array($cls->getName(), 'createEnumerable'),
                                                                      $x->values));
                              });
    }

    /**
     * {@inheritDoc}
     */
    public final function groupJoin($inner,
                                    $outerKeySelector, $innerKeySelector,
                                    $resultSelector,
                                    $keyEqualityComparer = null) {

        return static::createEnumerable($this->groupJoinInner($inner,
                                                              $outerKeySelector, $innerKeySelector,
                                                              $resultSelector,
                                                              $keyEqualityComparer));
    }

    /**
     * @see EnumerableBase::groupJoin()
     */
    protected function groupJoinInner($inner,
                                      $outerKeySelector, $innerKeySelector,
                                      $resultSelector,
                                      $keyEqualityComparer) {

        if (!($inner instanceof IEnumerable)) {
            $inner = static::createEnumerable($inner);
        }

        $keyEqualityComparer = static::getEqualComparerSafe($keyEqualityComparer);

        $createGrpsForSequence = function(IEnumerable $seq, $keySelector) {
            return $seq->groupBy($keySelector)
                       ->select(function(IGrouping $x) {
                                    $result         = new \stdClass();
                                    $result->key    = $x->key();
                                    $result->values = $x->getIterator();

                                    return $result;
                                })
                       ->toArray();
        };

        $outerGrps = \call_user_func($createGrpsForSequence,
                                     $this, $outerKeySelector);
        $innerGrps = \call_user_func($createGrpsForSequence,
                                     $inner, $innerKeySelector);

        $result = array();

        foreach ($outerGrps as $outerGrp) {
            foreach ($innerGrps as $innerGrp) {
                if (!\call_user_func($keyEqualityComparer,
                                     $outerGrp->key, $innerGrp->key)) {

                    continue;
                }

                foreach ($outerGrp->values as $outerVal) {
                    $result[] = \call_user_func($resultSelector,
                                                $outerVal, $innerGrp->values, $outerGrp->key, $innerGrp->key);
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public final function intersect($second, $equalityComparer = null) {
        return static::createEnumerable($this->intersectInner($second, $equalityComparer));
    }

    /**
     * @see EnumerableBase::intersect()
     */
    protected function intersectInner($second, $equalityComparer) {
        if (null === $second) {
            $second = array();
        }

        $equalityComparer = static::getEqualComparerSafe($equalityComparer);

        $secondArray = static::createEnumerable($second)
                             ->distinct($equalityComparer)
                             ->toArray();

        $result = array();

        while ($this->valid()) {
            $curItem = $this->current();

            // search for matching item in second sequence
            foreach ($secondArray as $k => $v) {
                if (!\call_user_func($equalityComparer, $v, $curItem)) {
                    // not found
                    continue;
                }

                unset($secondArray[$k]);
                $result[] = $curItem;

                break;
            }

            $this->next();
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty() {
        return !$this->valid();
    }

    /**
     * {@inheritDoc}
     */
    public final function isNotEmpty() {
        return !$this->isEmpty();
    }

    /**
     * {@inheritDoc}
     */
    public final function join($inner,
                               $outerKeySelector, $innerKeySelector,
                               $resultSelector,
                               $keyEqualityComparer = null) {

        return $this->joinInner($inner,
                                $outerKeySelector, $innerKeySelector,
                                $resultSelector,
                                $keyEqualityComparer);
    }

    /**
     * @see EnumerableBase::join()
     */
    protected function joinInner($inner,
                                 $outerKeySelector, $innerKeySelector,
                                 $resultSelector,
                                 $keyEqualityComparer) {

        if (!$inner instanceof IEnumerable) {
            $inner = static::createEnumerable($inner);
        }

        $keyEqualityComparer = static::getEqualComparerSafe($keyEqualityComparer);

        $createGrpsForSequence = function(IEnumerable $seq, $keySelector) {
            return $seq->groupBy(function ($item, $ctx) use ($keySelector) {
                                     return \call_user_func($keySelector,
                                                            $item, $ctx);
                                 })
                       ->select(function(IGrouping $x) {
                                    $result         = new \stdClass();
                                    $result->key    = $x->key();
                                    $result->values = $x->getIterator()
                                                        ->toArray();

                                    return $result;
                                })
                       ->toArray();
        };

        $outerGrps = \call_user_func($createGrpsForSequence,
                                     $this, $outerKeySelector);
        $innerGrps = \call_user_func($createGrpsForSequence,
                                     $inner, $innerKeySelector);

        $result = array();

        foreach ($outerGrps as $outerGrp) {
            foreach ($innerGrps as $innerGrp) {
                if (!\call_user_func($keyEqualityComparer,
                                     $outerGrp->key, $innerGrp->key)) {

                    continue;
                }

                foreach ($outerGrp->values as $outerVal) {
                    foreach ($innerGrp->values as $innerVal) {
                        $result[] = \call_user_func($resultSelector,
                                                    $outerVal, $innerVal, $outerGrp->key, $innerGrp->key);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function joinToString($separator, $defValue = '') {
        return ClrString::join($separator, $this, $defValue);
    }

    /**
     * {@inheritDoc}
     */
    public function key() {
        return $this->_i->key();
    }

    /**
     * {@inheritDoc}
     */
    public final function lastOrDefault($predicateOrDefValue = null, $defValue = null) {
        static::updatePredicateAndDefaultValue(\func_num_args(),
                                               $predicateOrDefValue, $defValue);

        $predicateOrDefValue = static::getPredicateSafe($predicateOrDefValue);

        $result = $defValue;

        $index = 0;
        while ($this->valid()) {
            $ctx = static::createContextObject($this, $index++);

            if (\call_user_func($predicateOrDefValue, $ctx->value, $ctx)) {
                $result = $ctx->value;
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public final function max($defValue = null, $comparer = null) {
        $comparer = static::getComparerSafe($comparer);

        return $this->aggregate(function($result, $curItem) use ($comparer) {
                                    // check if result item is smaller
                                    // than the current one

                                    return \call_user_func($comparer, $result, $curItem) < 0 ? $curItem
                                                                                             : $result;
                                }, $defValue);
    }

    /**
     * {@inheritDoc}
     */
    public final function min($defValue = null, $comparer = null) {
        $comparer = static::getComparerSafe($comparer);

        return $this->aggregate(function($result, $item) use ($comparer) {
                                    // check if result item is greater
                                    // than the current one

                                    return \call_user_func($comparer, $result, $item) > 0 ? $item
                                                                                          : $result;
                                }, $defValue);
    }

    /**
     * {@inheritDoc}
     */
    public function next() {
        $this->_i->next();
    }

    /**
     * {@inheritDoc}
     */
    public function ofType($type) {
        $type = \trim($type);

        return $this->where(function($x) use ($type) {
                                if ('' === $type) {
                                    return null === $x;
                                }

                                if (\is_object($x)) {
                                    if (\interface_exists($type) || \class_exists($type)) {
                                        $reflect = new \ReflectionClass($type);

                                        return $reflect->isInstance($x);
                                    }

                                    return 'object' === $type;
                                }

                                if ('scalar' === $type) {
                                    return \is_scalar($x);
                                }

                                return \gettype($x) == $type;
                            });
    }

    /**
     * {@inheritDoc}
     */
    public final function order($comparer = null) {
        return $this->orderBy(function($x) {
                                  return $x;
                              }, $comparer);
    }

    /**
     * {@inheritDoc}
     */
    public function orderBy($selector, $comparer = null) {
        $comparer = static::getComparerSafe($comparer);

        $result = $this->select(function($x, $ctx) use ($selector) {
                                    $result         = new \stdClass();
                                    $result->sortBy = \call_user_func($selector,
                                                                      $x, $ctx);
                                    $result->value  = $x;

                                    return $result;
                                })
                       ->toArray();

        \uasort($result, function($x, $y) use ($comparer) {
                             return \call_user_func($comparer,
                                                    $x->sortBy, $y->sortBy);
                         });

        return static::createEnumerable($result)
                     ->select(function($x) {
                                  return $x->value;
                              });
    }

    /**
     * {@inheritDoc}
     */
    public final function orderByDescending($selector, $comparer = null) {
        $comparer = static::getComparerSafe($comparer);

        return $this->orderBy($selector,
                              function($x, $y) use ($comparer) {
                                  return -1 * \call_user_func($comparer,
                                                              $x, $y);
                              });
    }

    /**
     * {@inheritDoc}
     */
    public final function orderDescending($comparer = null) {
        return $this->orderByDescending(function($x) {
                                            return $x;
                                        }, $comparer);
    }

    /**
     * {@inheritDoc}
     */
    public final function product($defValue = null) {
        return $this->aggregate(function($result, $item) {
                                    return $result * $item;
                                }, $defValue);
    }

    /**
     * {@inheritDoc}
     */
    public function randomize($seeder = null, $randProvider = null) {
        if (null === $randProvider) {
            $randProvider = function () {
                return \mt_rand();
            };
        }

        if (null !== $seeder) {
            \call_user_func($seeder);
        }

        return $this->orderBy($randProvider);
    }

    /**
     * {@inheritDoc}
     */
    public function reset() {
        $this->_i->rewind();
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public final function reverse() {
        return $this->orderBy(function($x, $ctx) {
                                  return \PHP_INT_MAX - $ctx->index;
                              });
    }

    /**
     * {@inheritDoc}
     */
    public final function rewind() {
        // deactivate
    }

    /**
     * {@inheritDoc}
     */
    public final function runtimeVersion() {
        return "5.3";
    }

    /**
     * {@inheritDoc}
     */
    public final function select($selector) {
        return static::createEnumerable($this->selectInner($selector));
    }

    /**
     * @see EnumerableBase::select()
     */
    protected function selectInner($selector) {
        $result = array();

        $index = 0;
        while ($this->valid()) {
            $ctx = static::createContextObject($this, $index++);

            $result[] = \call_user_func($selector,
                                        $ctx->value, $ctx);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public final function selectMany($selector) {
        return static::createEnumerable($this->selectManyInner($selector));
    }

    /**
     * @see EnumerableBase::selectMany()
     */
    protected function selectManyInner($selector) {
        $result = array();

        $index = 0;
        while ($this->valid()) {
            $ctx = static::createContextObject($this, $index++);

            $iterator = static::asIterator(\call_user_func($selector,
                                                           $ctx->value, $ctx));

            if (null === $iterator) {
                continue;
            }

            while ($iterator->valid()) {
                $result[] = $iterator->current();

                $iterator->next();
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public final function sequenceEqual($other, $equalityComparer = null) {
        $equalityComparer = static::getEqualComparerSafe($equalityComparer);

        $other = static::asIterator($other, true);

        while ($this->valid()) {
            $x = $this->current();
            $this->next();

            if (!$other->valid()) {
                // that sequence has more items
                return false;
            }

            $y = $other->current();
            $other->next();

            if (!\call_user_func($equalityComparer, $x, $y)) {
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

    /**
     * {@inheritDoc}
     */
    public function serialize() {
        return $this->toJson();
    }

    /**
     * {@inheritDoc}
     */
    public final function singleOrDefault($predicateOrDefValue = null, $defValue = null) {
        static::updatePredicateAndDefaultValue(\func_num_args(),
                                               $predicateOrDefValue, $defValue);

        $predicateOrDefValue = static::getPredicateSafe($predicateOrDefValue);

        $result = $defValue;

        $index = 0;
        $hasAlreadyBeenFound = false;
        while ($this->valid()) {
            $ctx = static::createContextObject($this, $index++);

            if (\call_user_func($predicateOrDefValue, $ctx->value, $ctx)) {
                if ($hasAlreadyBeenFound) {
                    $this->throwException('Sequence contains more than one matching element!');
                }

                $result              = $ctx->value;
                $hasAlreadyBeenFound = true;
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public final function skip($count) {
        return $this->skipWhile(function($x, $ctx) use ($count) {
                                    return $ctx->index < $count;
                                });
    }

    /**
     * {@inheritDoc}
     */
    public final function skipWhile($predicate) {
        $index = 0;
        while ($this->valid()) {
            $ctx = static::createContextObject($this, $index++, false);

            if (\call_user_func($predicate,
                                $ctx->value, $ctx)) {

                $this->next();
            }
            else {
                break;
            }
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public final function sum($defValue = null) {
        return $this->aggregate(function($result, $item) {
                                    return $result + $item;
                                }, $defValue);
    }

    /**
     * {@inheritDoc}
     */
    public final function take($count) {
        return $this->takeWhile(function($x, $ctx) use ($count) {
                                    return $ctx->index < $count;
                                });
    }

    /**
     * {@inheritDoc}
     */
    public final function takeWhile($predicate) {
        return static::createEnumerable($this->takeWhileInner($predicate));
    }

    /**
     * @see EnumerableBase::takeWhile()
     */
    protected function takeWhileInner($predicate) {
        $result = array();

        $index = 0;
        while ($this->valid()) {
            $ctx = static::createContextObject($this, $index++, false);

            if (\call_user_func($predicate,
                                $ctx->value, $ctx)) {

                $result[] = $ctx->value;
                $this->next();
            }
            else {
                break;
            }
        }

        return $result;
    }

    /**
     * Throws an exception for that sequence.
     *
     * @param string $message The message.
     * @param int $code The code.
     * @param \Exception $previous The inner/previous exception.
     *
     * @throws EnumerableException The thrown exception.
     */
    protected function throwException($message = null,
                                      $code = 0,
                                      \Exception $previous = null) {

        throw new EnumerableException($this,
                                      $message, $previous, $code);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray($keySelector = null) {
        if (null === $keySelector) {
            $keySelector = function() {
                return null;
            };
        }

        $result = array();

        $index = 0;
        while ($this->valid()) {
            $ctx = static::createContextObject($this, $index++);

            $key = \call_user_func($keySelector,
                                   $ctx->key, $ctx->value, $ctx);

            if (null === $key) {
                // autokey
                $result[] = $ctx->value;
            }
            else {
                $result[$key] = $ctx->value;
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function toDictionary($keySelector = null, $keyEqualityComparer = null) {
        if (null === $keySelector) {
            $keySelector = function($key) {
                return $key;
            };
        }

        $result = new Dictionary(null, $keyEqualityComparer);

        $index = 0;
        while ($this->valid()) {
            $ctx = static::createContextObject($this, $index++);

            $result->add(\call_user_func($keySelector,
                                         $ctx->key, $ctx->value, $ctx),
                         $ctx->value);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function toJson($keySelector = null, $options = null) {
        if (\func_num_args() == 1) {
            if ((null !== $keySelector) && !\is_callable($keySelector)) {
                // swap values

                $options     = $keySelector;
                $keySelector = null;
            }
        }

        if (null === $keySelector) {
            $keySelector = function($key) {
                return $key;
            };
        }

        if (null === $options) {
            $options = 0;
        }

        return \json_encode($this->toArray($keySelector),
                            (int)$options);
    }

    /**
     * {@inheritDoc}
     */
    public final function toList() {
        return new Collection($this);
    }

    /**
     * {@inheritDoc}
     */
    public final function toLookup($keySelector = null, $keyEqualityComparer = null,
                                   $elementSelector = null) {

        $elements = $this;
        if (null !== $elementSelector) {
            $elements = $elements->select($elementSelector);
        }

        return new Lookup($elements->groupBy($keySelector,
                                             $keyEqualityComparer));
    }

    /**
     * {@inheritDoc}
     */
    public final function toSet($equalityComparer = null) {
        $result = new Set($equalityComparer);

        while ($this->valid()) {
            $result->add($this->current());

            $this->next();
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function toString() {
        return $this->toJson();
    }

    /**
     * {@inheritDoc}
     */
    public final function union($second, $equalityComparer = null) {
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
            if (!\is_callable($predicate)) {
                // use $predicate as default value

                $defValue  = $predicate;
                $predicate = null;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized) {
        $iterator = static::asIterator(\json_decode($serialized, true),
                                       true);

        $this->__construct($iterator);
    }

    /**
     * {@inheritDoc}
     */
    public function valid() {
        return $this->_i->valid();
    }

    /**
     * {@inheritDoc}
     */
    public final function where($predicate) {
        return static::createEnumerable($this->whereInner($predicate));
    }

    /**
     * @see EnumerableBase::where()
     */
    protected function whereInner($predicate) {
        $result = array();

        $index = 0;
        while ($this->valid()) {
            $ctx = static::createContextObject($this, $index++);

            if (\call_user_func($predicate,
                                $ctx->value, $ctx)) {

                $result[] = $ctx->value;
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public final function zip($second, $selector) {
        return static::createEnumerable($this->zipInner($second, $selector));
    }

    /**
     * @see EnumerableBase::zip()
     */
    protected function zipInner($second, $selector) {
        $second = static::asIterator($second, true);

        $result = array();

        $index = 0;
        while ($this->valid() && $second->valid()) {
            $ctx1 = static::createContextObject($this, $index);
            $ctx2 = static::createContextObject($second, $index);
            ++$index;

            $result[] = \call_user_func($selector,
                                        $ctx1->value, $ctx2->value, $ctx1, $ctx2);
        }

        return $result;
    }
}
