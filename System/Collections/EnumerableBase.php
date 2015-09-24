<?php

/**********************************************************************************************************************
 * phpLINQ (https://github.com/mkloubert/phpLINQ)                                                                     *
 *                                                                                                                    *
 * Copyright (c) 2015, Marcel Joachim Kloubert <marcel.kloubert@gmx.net>                                              *
 * All rights reserved.                                                                                               *
 *                                                                                                                    *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the   *
 * following conditions are met:                                                                                      *
 *                                                                                                                    *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the          *
 *    following disclaimer.                                                                                           *
 *                                                                                                                    *
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the       *
 *    following disclaimer in the documentation and/or other materials provided with the distribution.                *
 *                                                                                                                    *
 * 3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote    *
 *    products derived from this software without specific prior written permission.                                  *
 *                                                                                                                    *
 *                                                                                                                    *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, *
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE  *
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, *
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR    *
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,  *
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE   *
 * USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.                                           *
 *                                                                                                                    *
 **********************************************************************************************************************/

namespace System\Collections;

use \System\ArgumentException;
use \System\ArgumentNullException;
use \System\ArgumentOutOfRangeException;
use \System\ClrString;
use \System\FormatException;
use \System\IObject;
use \System\IComparable;
use \System\IString;
use \System\Linq\Grouping;
use \System\Linq\IGrouping;
use \System\Linq\IOrderedEnumerable;
use \System\Linq\OrderedEnumerable;
use \System\Object;


/**
 * Describes a sequence.
 *
 * @package System\Collections
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
abstract class EnumerableBase extends Object implements IEnumerable {
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
        if (null === $accumulator) {
            throw new ArgumentNullException('accumulator');
        }

        $accumulator = static::asCallable($accumulator);

        return $this->iterateWithItemContext(function($x, IEachItemContext $ctx) use ($accumulator) {
                                                 if (!$ctx->isFirst()) {
                                                     $ctx->result($accumulator($ctx->result(), $x, $ctx));
                                                 }
                                                 else {
                                                     $ctx->result($x);
                                                 }
                                             }, $defValue);
    }

    /**
     * {@inheritDoc}
     */
    public final function all($predicate) : bool {
        if (null === $predicate) {
            throw new ArgumentNullException('predicate');
        }

        $predicate = static::wrapPredicate(static::asCallable($predicate));

        return $this->iterateWithItemContext(function($x, IEachItemContext $ctx) use ($predicate) {
                                                 if ($predicate($x, $ctx)) {
                                                     return;
                                                 }

                                                 $ctx->result(false);
                                                 $ctx->cancel(true);
                                             }, true);
    }

    /**
     * {@inheritDoc}
     */
    public final function any($predicate = null) : bool {
        $predicate = static::getPredicateSafe($predicate);

        return $this->iterateWithItemContext(function($x, IEachItemContext $ctx) use ($predicate) {
                                                 if (!$predicate($x, $ctx)) {
                                                     return;
                                                 }

                                                 $ctx->result(true);
                                                 $ctx->cancel(true);
                                             }, false);
    }

    /**
     * {@inheritDoc}
     */
    public final function appendToArray(array &$arr, bool $withKeys = false) : IEnumerable {
        return $this->iterateWithItemContext(function($x, IItemContext $ctx) use (&$arr, $withKeys) {
                                                 if (!$withKeys) {
                                                     $arr[] = $x;
                                                 }
                                                 else {
                                                     $arr[$ctx->key()] = $x;
                                                 }
                                             }, $this);
    }

    /**
     * Returns an value as callable.
     *
     * @param mixed $val The input value.
     *
     * @return callable The output value.
     *
     * @throws ArgumentException $val is invalid.
     */
    public static function asCallable($val) {
        if (\is_callable($val) || (null === $val)) {
            return $val;
        }

        return static::toLambda($val);
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
            if (\is_array($obj)) {
                return new \ArrayIterator($obj);
            }
            else if ($obj instanceof \IteratorAggregate) {
                $obj = $obj->getIterator();
                continue;
            }
            else if (\is_string($obj)) {
                return new ClrString($obj);
            }

            break;
        }

        if (null === $obj) {
            if (!$emptyIfNull) {
                return null;
            }
            else {
                return new \EmptyIterator();
            }
        }

        if ($obj instanceof \Iterator) {
            // nothing to convert
            return $obj;
        }

        if ($obj instanceof \Traversable) {
            $arr = \iterator_to_array($obj);
        }
        else {
            $arr = [$obj];
        }

        return new \ArrayIterator($arr);
    }

    /**
     * {@inheritDoc}
     */
    public function asResettable() : IEnumerable {
        switch (\get_class($this->_i)) {
            case \Generator::class:
                return static::createEnumerable($this->toArray(true));
                break;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public final function average($defValue = null) {
        $count = 0;
        $sum = $this->each(function($x, IEachItemContext $ctx) use (&$count) {
                               $count = $ctx->index() + 1;

                               $ctx->result(!$ctx->isFirst() ? ($ctx->result() + $x)
                                                             : $x);
                           });

        return $count > 0 ? ((float)$sum / (float)$count)
                          : $defValue;
    }

    /**
     * {@inheritDoc}
     */
    public final function cast($type) : IEnumerable {
        $type     = \trim($type);
        $castCode = \sprintf('return (%s)$x;', $type);
        $cls      = $this->getType();

        $myMethods = [
            'asCallable' => $cls->getMethod('asCallable')->getClosure(null),
            'isCallable' => $cls->getMethod('isCallable')->getClosure(null),
        ];

        return $this->select(function($x) use ($castCode, $myMethods, $type) {
                                 switch ($type) {
                                     case 'callable':
                                     case 'function':
                                         if ($myMethods['isCallable']($x)) {
                                             // is already callable
                                             return $myMethods['asCallable']($x);
                                         }

                                         // wrap
                                         return function() use ($x) {
                                             return $x;
                                         };
                                         break;
                                 }

                                 return eval($castCode);
                             });
    }

    /**
     * {@inheritDoc}
     */
    public final function concat() : IEnumerable {
        return static::createEnumerable($this->concatInner(\func_get_args()));
    }

    /**
     * @see EnumerableBase::concat()
     */
    protected function concatInner(array $itemLists) {
        // first the values of that sequence
        while ($this->valid()) {
            yield $this->current();
            $this->next();
        }

        // now the items from the lists
        foreach ($itemLists as $items) {
            $iterator = static::asIterator($items);
            if (null === $iterator) {
                continue;
            }

            while ($iterator->valid()) {
                yield $iterator->current();
                $iterator->next();
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public final function concatToString($defValue = '') : IString {
        return $this->joinToString('', $defValue);
    }

    /**
     * {@inheritDoc}
     */
    public final function concatValues() : IEnumerable {
        return $this->concat(\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public final function contains($item, $equalityComparer = null) : bool {
        $equalityComparer = static::getEqualityComparerSafe($equalityComparer);

        return $this->any(function($x) use ($equalityComparer, $item) : bool {
                              return $equalityComparer($x, $item);
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
     * Creates a new sequence.
     *
     * @param mixed $items The items of the sequence.
     *
     * @return IEnumerable The new sequence.
     */
    protected static function createEnumerable($items = null) {
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
    public function defaultArrayIfEmpty($items = null) : IEnumerable {
        if ($this->isEmpty()) {
            return static::createEnumerable($items);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function defaultIfEmpty() : IEnumerable {
        return $this->defaultArrayIfEmpty(\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function distinct($equalityComparer = null) : IEnumerable  {
        $equalityComparer = static::getEqualityComparerSafe($equalityComparer);

        $result = [];

        while ($this->valid()) {
            $curItem = $this->current();

            // search for duplicate
            $alreadyInList = false;
            foreach ($result as $existingItem) {
                if (!$equalityComparer($curItem, $existingItem)) {
                    continue;
                }

                // found duplicate
                $alreadyInList = true;
                break;
            }

            if (!$alreadyInList) {
                $result[] = $curItem;
            }

            $this->next();
        }

        return static::createEnumerable($result);
    }

    /**
     * {@inheritDoc}
     */
    public final function each($action, $defResult = null) {
        if (null === $action) {
            throw new ArgumentNullException('action');
        }

        $action = static::asCallable($action);

        $result = $defResult;

        $index   = 0;
        $prevVal = null;
        $value   = null;
        while ($this->valid()) {
            $ctx = new EachItemContext($this, $index++, true, $prevVal);
            $ctx->result($result);
            $ctx->value($value);

            $action($ctx->item(), $ctx);

            $result = $ctx->result();

            if ($ctx->cancel()) {
                break;
            }

            $prevVal = $ctx->nextValue();
            $value   = $ctx->value();
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function elementAtOrDefault(int $index, $defValue = null) {
        return $this->skip($index)
                    ->firstOrDefault(null, $defValue);
    }

    /**
     * {@inheritDoc}
     */
    public final function except($second, $equalityComparer = null) : IEnumerable {
        $equalityComparer = static::getEqualityComparerSafe($equalityComparer);

        return static::createEnumerable($this->exceptInner($second, $equalityComparer));
    }

    /**
     * @see EnumerableBase::except()
     */
    protected function exceptInner($second, callable $equalityComparer) {
        if (!$second instanceof IEnumerable) {
            $second = static::createEnumerable($second);
        }

        $second = $second->distinct($equalityComparer)
                         ->toArray();

        while ($this->valid()) {
            $curItem = $this->current();

            $found = false;
            foreach ($second as $ite) {
                if (!$equalityComparer($ite, $curItem)) {
                    continue;
                }

                $found = true;
                break;
            }

            if (!$found) {
                yield $curItem;
            }

            $this->next();
        }
    }

    /**
     * {@inheritDoc}
     */
    public final function firstOrDefault($predicateOrDefValue = null, $defValue = null) {
        static::updatePredicateAndDefaultValue(\func_num_args(),
                                               $predicateOrDefValue, $defValue);

        $predicateOrDefValue = static::getPredicateSafe($predicateOrDefValue);

        return $this->iterateWithItemContext(function($x, IEachItemContext $ctx) use ($predicateOrDefValue) {
                                                 if (!$predicateOrDefValue($x, $ctx)) {
                                                     return;
                                                 }

                                                 $ctx->result($x);
                                                 $ctx->cancel(true);
                                             }, $defValue);
    }

    /**
     * Keeps sure that a comparer function is NOT (null).
     *
     * @param callable $comparer The input value.
     *
     * @return callable The output value.
     */
    protected static function getComparerSafe($comparer) : callable {
        $comparer = static::asCallable($comparer);

        $defaultComparer = function($x, $y) : int {
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

        if (null === $comparer) {
            return $defaultComparer;
        }

        $rf = static::toReflectionFunction($comparer);
        if ($rf->getNumberOfParameters() < 2) {
            // use function as selector

            return function($x, $y) use ($defaultComparer, $comparer) : int {
                return $defaultComparer($comparer($x),
                                        $comparer($y));
            };
        }

        return function($x, $y) use ($comparer) : int {
            return $comparer($x, $y);
        };
    }

    /**
     * Keeps sure that a equality comparer is NOT (null).
     *
     * @param callable $equalityComparer The input value.
     *
     * @return callable The output value.
     */
    public static function getEqualityComparerSafe($equalityComparer) : callable {
        $equalityComparer = static::asCallable($equalityComparer);

        $defaultEqualityComparer = function($x, $y) : bool {
            if ($x instanceof IObject) {
                return $x->equals($y);
            }
            else if ($y instanceof IObject) {
                return $y->equals($x);
            }

            return $x == $y;
        };

        if (null === $equalityComparer) {
            return $defaultEqualityComparer;
        }

        $rf = static::toReflectionFunction($equalityComparer);
        if ($rf->getNumberOfParameters() < 2) {
            // use function as selector

            return function($x, $y) use ($defaultEqualityComparer, $equalityComparer) : bool {
                return $defaultEqualityComparer($equalityComparer($x),
                                                $equalityComparer($y));
            };
        }

        return function($x, $y) use ($equalityComparer) : bool {
            return $equalityComparer($x, $y);
        };
    }

    /**
     * Keeps sure that a predicate function is NOT (null).
     *
     * @param callable $predicate The input value.
     *
     * @return callable The output value.
     */
    protected static function getPredicateSafe($predicate) : callable {
        $predicate = static::asCallable($predicate);

        if (null === $predicate) {
            return function() : bool {
                return true;
            };
        }

        return static::wrapPredicate($predicate);
    }

    /**
     * {@inheritDoc}
     */
    public final function groupBy($keySelector, $keyEqualityComparer = null) : IEnumerable {
        if (null === $keySelector) {
            throw new ArgumentNullException('keySelector');
        }

        $keySelector         = static::asCallable($keySelector);
        $keyEqualityComparer = static::getEqualityComparerSafe($keyEqualityComparer);

        $groups = $this->iterateWithItemContext(function($x, IEachItemContext $ctx) use ($keyEqualityComparer, $keySelector) {
            $groupList = $ctx->result();

            $key = $keySelector($x, $ctx);

            $grp = null;
            foreach ($groupList as $g) {
                if ($keyEqualityComparer($g->key, $key)) {
                    $grp = $g;
                    break;
                }
            }

            if (null === $grp) {
                $grp         = new \stdClass();
                $grp->key    = $key;
                $grp->values = [];

                $groupList[] = $grp;
            }

            $grp->values[] = $x;

            $ctx->result($groupList);
        }, []);

        $cls       = $this->getType();
        $createSeq = $cls->getMethod('createEnumerable')->getClosure(null);

        return $createSeq($groups)->select(function(\stdClass $x) use ($createSeq) : IGrouping {
                                               return new Grouping($x->key,
                                                                   $createSeq($x->values));
                                           });
    }

    /**
     * {@inheritDoc}
     */
    public final function groupJoin($inner,
                                    $outerKeySelector, $innerKeySelector,
                                    $resultSelector,
                                    $keyEqualityComparer = null) : IEnumerable {

        if (null === $outerKeySelector) {
            throw new ArgumentNullException('outerKeySelector');
        }

        if (null === $innerKeySelector) {
            throw new ArgumentNullException('innerKeySelector');
        }

        if (null === $resultSelector) {
            throw new ArgumentNullException('resultSelector');
        }

        $outerKeySelector = static::asCallable($outerKeySelector);
        $innerKeySelector = static::asCallable($innerKeySelector);
        $resultSelector   = static::asCallable($resultSelector);

        return static::createEnumerable($this->groupJoinInner($inner,
                                                              $outerKeySelector, $innerKeySelector,
                                                              $resultSelector,
                                                              static::getEqualityComparerSafe($keyEqualityComparer)));
    }

    /**
     * @see EnumerableBase::groupJoin()
     */
    protected function groupJoinInner($inner,
                                      callable $outerKeySelector, callable $innerKeySelector,
                                      callable $resultSelector,
                                      callable $keyEqualityComparer) {

        if (!$inner instanceof IEnumerable) {
            $inner = static::createEnumerable($inner);
        }

        $createGroupsForSequence = function(IEnumerable $seq, $keySelector) : array {
            return $seq->groupBy($keySelector)
                       ->select(function(IGrouping $x) {
                                    $result         = new \stdClass();
                                    $result->key    = $x->key();
                                    $result->values = $x->getIterator();

                                    return $result;
                                })
                       ->toArray();
        };

        $outerGroups = $createGroupsForSequence($this , $outerKeySelector);
        $innerGroups = $createGroupsForSequence($inner, $innerKeySelector);

        foreach ($outerGroups as $outerGrp) {
            foreach ($innerGroups as $innerGrp) {
                if (!$keyEqualityComparer($outerGrp->key, $innerGrp->key)) {
                    continue;
                }

                foreach ($outerGrp->values as $outerVal) {
                    yield $resultSelector($outerVal, $innerGrp->values, $outerGrp->key, $innerGrp->key);
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public final function intersect($second, $equalityComparer = null) : IEnumerable {
        $equalityComparer = static::getEqualityComparerSafe($equalityComparer);

        return static::createEnumerable($this->intersectInner($second, $equalityComparer));
    }

    /**
     * @see EnumerableBase::intersect()
     */
    protected function intersectInner($second, callable $equalityComparer) {
        if (!$second instanceof IEnumerable) {
            $second = static::createEnumerable($second);
        }

        $second = $second->distinct($equalityComparer)
                         ->toArray();

        while ($this->valid()) {
            $curItem = $this->current();

            // search for matching item in second sequence
            foreach ($second as $k => $v) {
                if (!$equalityComparer($v, $curItem)) {
                    // not found
                    continue;
                }

                unset($second[$k]);
                yield $curItem;

                $this->next();
                break;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty() : bool {
        return !$this->valid();
    }

    /**
     * {@inheritDoc}
     */
    public final function isNotEmpty() : bool {
        return !$this->isEmpty();
    }

    /**
     * {@inheritDoc}
     */
    public final function join($inner,
                               $outerKeySelector, $innerKeySelector,
                               $resultSelector,
                               $keyEqualityComparer = null) : IEnumerable {

        if (null === $outerKeySelector) {
            throw new ArgumentNullException('outerKeySelector');
        }

        if (null === $innerKeySelector) {
            throw new ArgumentNullException('innerKeySelector');
        }

        if (null === $resultSelector) {
            throw new ArgumentNullException('resultSelector');
        }

        $outerKeySelector = static::asCallable($outerKeySelector);
        $innerKeySelector = static::asCallable($innerKeySelector);
        $resultSelector   = static::asCallable($resultSelector);

        return static::createEnumerable($this->joinInner($inner,
                                                         $outerKeySelector, $innerKeySelector,
                                                         $resultSelector,
                                                         static::getEqualityComparerSafe($keyEqualityComparer)));
    }

    /**
     * @see EnumerableBase::join()
     */
    protected function joinInner($inner,
                                 callable $outerKeySelector, callable $innerKeySelector,
                                 callable $resultSelector,
                                 callable $keyEqualityComparer) {

        if (!$inner instanceof IEnumerable) {
            $inner = static::createEnumerable($inner);
        }

        $createGroupsForSequence = function(IEnumerable $seq, $keySelector) : array {
            return $seq->groupBy($keySelector)
                       ->select(function(IGrouping $x) {
                                    $result         = new \stdClass();
                                    $result->key    = $x->key();
                                    $result->values = $x->getIterator()
                                                        ->toArray();

                                    return $result;
                                })
                        ->toArray();
        };

        $outerGroups = $createGroupsForSequence($this , $outerKeySelector);
        $innerGroups = $createGroupsForSequence($inner, $innerKeySelector);

        foreach ($outerGroups as $outerGrp) {
            foreach ($innerGroups as $innerGrp) {
                if (!$keyEqualityComparer($outerGrp->key, $innerGrp->key)) {
                    continue;
                }

                foreach ($outerGrp->values as $outerVal) {
                    foreach ($innerGrp->values as $innerVal) {
                        yield $resultSelector($outerVal, $innerVal, $outerGrp->key, $innerGrp->key);
                    }
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public final function joinToString($separator = null, $defValue = '') : IString {
        return $this->joinToStringCallback(function() use ($separator) {
                                               return $separator;
                                           }, $defValue);
    }

    /**
     * {@inheritDoc}
     */
    public final function joinToStringCallback($separatorFactory = null, $defValue = '') : IString {
        if (null === $separatorFactory) {
            $separatorFactory = function() {
                return '';
            };
        }

        $separatorFactory = static::asCallable($separatorFactory);

        $result = $this->iterateWithItemContext(function($x, IEachItemContext $ctx) use ($separatorFactory) {
            $str = $ctx->result();

            if (!$ctx->isFirst()) {
                $str .= $separatorFactory($x, $ctx);
            }
            else {
                $str = '';
            }

            $str .= ClrString::valueToString($x);

            $ctx->result($str);
        }, $defValue);

        return new ClrString($result);
    }

    /**
     * Checks if a value can be executed / is callable.
     *
     * @param mixed $val The value to check.
     *
     * @return bool Can be executed or not.
     */
    public static function isCallable($val) {
        return \is_callable($val) ||
               static::isLambda($val);
    }

    /**
     * Checks if a value is a valid lambda expression.
     *
     * @param mixed $val The value to check.
     *
     * @return bool Is valid lambda expression or not.
     */
    public static function isLambda($val) {
        return false !== static::toLambda($val, false);
    }

    /**
     * Iterates over that sequence by using an item context.
     *
     * @param callable $action The action to invoke for each item.
     * @param mixed $initialResult The initial result value.
     * @param mixed $initialValue The initial iteration value.
     *
     * @return mixed The result of the iteration.
     */
    protected function iterateWithItemContext(callable $action, $initialResult = null, $initialValue = null) {
        $index   = 0;
        $prevVal = null;
        $result  = $initialResult;
        $value   = $initialValue;
        while ($this->valid()) {
            $ctx = new EachItemContext($this, $index++, true, $prevVal);
            $ctx->result($result);
            $ctx->value($value);

            $actionRes = $action($ctx->item(), $ctx);
            if (null === $ctx->nextValue()) {
                if ($ctx->nextValue() !== $actionRes) {
                    $ctx->nextValue($actionRes);
                }
            }

            $result = $ctx->result();

            if ($ctx->cancel()) {
                break;
            }

            $prevVal = $ctx->nextValue();
            $value   = $ctx->value();
        }

        return $result;
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

        return $this->iterateWithItemContext(function($x, IEachItemContext $ctx) use ($predicateOrDefValue) {
                                                 if (!$predicateOrDefValue($x, $ctx)) {
                                                     return;
                                                 }

                                                 $ctx->result($x);
                                             }, $defValue);
    }

    /**
     * {@inheritDoc}
     */
    public final function max($defValue = null, $comparer = null) {
        $comparer = static::getComparerSafe($comparer);

        return $this->aggregate(function($result, $curItem) use ($comparer) {
                                    // check if result item is smaller
                                    // than the current one

                                    return $comparer($result, $curItem) < 0 ? $curItem
                                                                            : $result;
                                }, $defValue);
    }

    /**
     * {@inheritDoc}
     */
    public final function min($defValue = null, $comparer = null) {
        $comparer = static::getComparerSafe($comparer);

        return $this->aggregate(function($result, $curItem) use ($comparer) {
                                    // check if result item is greater
                                    // than the current one

                                    return $comparer($result, $curItem) > 0 ? $curItem
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
    public function ofType($type) : IEnumerable {
        $type = \trim($type);

        return $this->where(function($x) use ($type) : bool {
                                if ('' === $type) {
                                    return null === $x;
                                }

                                if (\is_object($x)) {
                                    if (\interface_exists($type) || \class_exists($type)) {
                                        $rc = new \ReflectionClass($type);

                                        return $rc->isInstance($x);
                                    }

                                    if ('callable' === $type) {
                                        return \is_callable($x);
                                    }

                                    return 'object' === $type;
                                }

                                switch ($type) {
                                    case 'bool':
                                        return \is_bool($x);
                                        break;

                                    case 'int':
                                        return \is_int($x);
                                        break;

                                    case 'scalar':
                                        return \is_scalar($x);
                                        break;

                                    case 'callable':
                                        return \is_callable($x);
                                        break;

                                    case 'null':
                                        return null === $x;
                                        break;
                                }

                                return \gettype($x) === $type;
                            });
    }

    /**
     * {@inheritDoc}
     */
    public final function order($comparer = null, bool $preventKeys = false) : IOrderedEnumerable {
        static::updateOrderArguments(\func_num_args(), 1, $comparer, $preventKeys);

        return $this->orderBy(true, $comparer, $preventKeys);
    }

    /**
     * {@inheritDoc}
     */
    public function orderBy($selector, $comparer = null, bool $preventKeys = false) : IOrderedEnumerable {
        static::updateOrderArguments(\func_num_args(), 2, $comparer, $preventKeys);

        if (true === $selector) {
            $selector = function($x) {
                return $x;
            };
        }

        return new OrderedEnumerable($this,
                                     static::asCallable($selector),
                                     static::getComparerSafe($comparer),
                                     $preventKeys);
    }

    /**
     * {@inheritDoc}
     */
    public final function orderByDescending($selector, $comparer = null, bool $preventKeys = false) : IOrderedEnumerable {
        static::updateOrderArguments(\func_num_args(), 2, $comparer, $preventKeys);

        $comparer = static::getComparerSafe($comparer);

        return $this->orderBy($selector,
                              function($x, $y) use ($comparer) : int {
                                  return $comparer($y, $x);
                              },
                              $preventKeys);
    }

    /**
     * {@inheritDoc}
     */
    public final function orderDescending($comparer = null, bool $preventKeys = false) : IOrderedEnumerable {
        static::updateOrderArguments(\func_num_args(), 1, $comparer, $preventKeys);

        return $this->orderByDescending(true, $comparer, $preventKeys);
    }

    /**
     * {@inheritDoc}
     */
    public final function product($defValue = null) {
        return $this->aggregate(function($result, $x) {
                                    return $result * $x;
                                }, $defValue);
    }

    /**
     * {@inheritDoc}
     */
    public function randomize(
        $seeder = null,
        $randProviderOrPreventKeys = null,
        bool $preventKeys = false
    ) : IOrderedEnumerable {

        if (\func_num_args() < 3) {
            if (\is_bool($randProviderOrPreventKeys)) {
                $preventKeys               = $randProviderOrPreventKeys;
                $randProviderOrPreventKeys = null;
            }
        }

        if (true === $seeder) {
            $seeder = function() {
                list($usec, $sec) = \explode(' ', \microtime());
                return (float)$sec + ((float)$usec * 100000);
            };
        }

        $seeder                    = static::asCallable($seeder);
        $randProviderOrPreventKeys = static::asCallable($randProviderOrPreventKeys);

        if (null === $randProviderOrPreventKeys) {
            $randProviderOrPreventKeys = function () {
                return \mt_rand();
            };
        }

        if (null !== $seeder) {
            $seeder();
        }

        return $this->orderBy($randProviderOrPreventKeys, null, $preventKeys);
    }

    /**
     * {@inheritDoc}
     */
    public final function reset() : IEnumerable {
        $this->resetInner();
        return $this;
    }

    /**
     * EnumerableBase::reset()
     */
    protected function resetInner() {
        $this->_i->rewind();
    }

    /**
     * {@inheritDoc}
     */
    public final function reverse(bool $preventKeys = false) : IOrderedEnumerable {
        return $this->orderBy(function($x, IIndexedItemContext $ctx) {
                                  return \PHP_INT_MAX - $ctx->index();
                              },
                              null,
                              $preventKeys);
    }

    /**
     * {@inheritDoc}
     */
    public function rewind() {
        // deactivated by default
    }

    /**
     * {@inheritDoc}
     */
    public final function select($selector) : IEnumerable {
        if (null === $selector) {
            throw new ArgumentNullException('selector');
        }

        return static::createEnumerable($this->selectInner(static::asCallable($selector)));
    }

    /**
     * @see EnumerableBase::select()
     */
    protected function selectInner(callable $selector) {
        $index   = 0;
        $prevVal = null;
        $value   = null;
        while ($this->valid()) {
            $ctx = new EachItemContext($this, $index++, true, $prevVal);
            $ctx->value($value);

            $newItem = $selector($ctx->item(), $ctx);

            if ($ctx->cancel()) {
                break;
            }

            yield $newItem;

            $prevVal = $ctx->nextValue();
            $value   = $ctx->value();
        }
    }

    /**
     * {@inheritDoc}
     */
    public final function selectMany($selector) : IEnumerable {
        if (null === $selector) {
            throw new ArgumentNullException('selector');
        }

        return static::createEnumerable($this->selectManyInner(static::asCallable($selector)));
    }

    /**
     * @see EnumerableBase::selectMany()
     */
    public function selectManyInner(callable $selector) {
        $index   = 0;
        $prevVal = null;
        $value   = null;
        while ($this->valid()) {
            $ctx = new EachItemContext($this, $index++, true, $prevVal);
            $ctx->value($value);

            $iterator = static::asIterator($selector($ctx->item(), $ctx));

            if ($ctx->cancel()) {
                break;
            }

            if (null === $iterator) {
                continue;
            }

            while ($iterator->valid()) {
                yield $iterator->current();

                $iterator->next();
            }

            $prevVal = $ctx->nextValue();
            $value   = $ctx->value();
        }
    }

    /**
     * {@inheritDoc}
     */
    public final function sequenceEqual($other, $equalityComparer = null) : bool {
        $equalityComparer = static::getEqualityComparerSafe($equalityComparer);

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

            if (!$equalityComparer($x, $y)) {
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
    public function serialize() : string {
        return \serialize($this->toArray(true));
    }

    /**
     * {@inheritDoc}
     */
    public final function singleOrDefault($predicateOrDefValue = null, $defValue = null) {
        static::updatePredicateAndDefaultValue(\func_num_args(),
                                               $predicateOrDefValue, $defValue);

        $predicateOrDefValue = static::getPredicateSafe($predicateOrDefValue);

        $me = $this;

        return $this->iterateWithItemContext(function($x, IEachItemContext $ctx) use ($me, $predicateOrDefValue) {
                                                 if (!$predicateOrDefValue($x, $ctx)) {
                                                     return;
                                                 }

                                                 if (true === $ctx->value()) {
                                                     $te = $me->getType()
                                                              ->getMethod('throwException')
                                                              ->getClosure($me);

                                                     $te('Sequence contains more than one matching element!');
                                                 }

                                                 $ctx->result($x);
                                                 $ctx->value(true);
                                             }, $defValue);
    }

    /**
     * {@inheritDoc}
     */
    public final function skip(int $count) : IEnumerable {
        if ($count < 0) {
            throw new ArgumentOutOfRangeException('count', $count);
        }

        return $this->skipWhile(function() use (&$count) {
                                    return $count-- > 0;
                                });
    }

    /**
     * {@inheritDoc}
     */
    public final function skipWhile($predicate) : IEnumerable {
        if (null === $predicate) {
            throw new ArgumentNullException('predicate');
        }

        $predicate = static::wrapPredicate($predicate);

        $index   = 0;
        $prevVal = null;
        $value   = null;
        while ($this->valid()) {
            $ctx = new EachItemContext($this, $index++, false, $prevVal);
            $ctx->value($value);

            if ($predicate($ctx->item(), $ctx)) {
                $this->next();
            }
            else {
                break;
            }

            if ($ctx->cancel()) {
                break;
            }

            $prevVal = $ctx->nextValue();
            $value   = $ctx->value();
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public final function sum($defValue = null) {
        return $this->aggregate(function($result, $x) {
                                    return $result + $x;
                                }, $defValue);
    }

    /**
     * {@inheritDoc}
     */
    public final function take(int $count) : IEnumerable {
        if ($count < 0) {
            throw new ArgumentOutOfRangeException('count', $count);
        }

        return $this->takeWhile(function() use (&$count) {
                                    return $count-- > 0;
                                });
    }

    /**
     * {@inheritDoc}
     */
    public final function takeWhile($predicate) : IEnumerable {
        if (null === $predicate) {
            throw new ArgumentNullException('predicate');
        }

        return static::createEnumerable($this->takeWhileInner(static::asCallable($predicate)));
    }

    /**
     * @see EnumerableBase::takeWhile()
     */
    protected function takeWhileInner(callable $predicate) {
        $predicate = static::wrapPredicate($predicate);

        $index   = 0;
        $prevVal = null;
        $value   = null;
        while ($this->valid()) {
            $ctx = new EachItemContext($this, $index++, false, $prevVal);
            $ctx->value($value);

            if ($predicate($ctx->item(), $ctx)) {
                yield $ctx->item();
                $this->next();
            }
            else {
                break;
            }

            if ($ctx->cancel()) {
                break;
            }

            $prevVal = $ctx->nextValue();
            $value   = $ctx->value();
        }
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
    protected function throwException(string $message = null,
                                      int $code = 0,
                                      \Exception $previous = null) {

        throw new EnumerableException($this,
                                      $message, $previous, $code);
    }

    /**
     * {@inheritDoc}
     */
    public final function toArray($keySelector = null) : array {
        if (true === $keySelector) {
            $keySelector = function($key) {
                return $key;
            };
        }

        $keySelector = static::asCallable($keySelector);

        $result = [];

        $this->iterateWithItemContext(function($x, IItemContext $ctx) use ($keySelector, &$result) {
                                          if (null === $keySelector) {
                                              // autokey
                                              $result[] = $x;
                                          }
                                          else {
                                              $result[$keySelector($ctx->key(), $x, $ctx)] = $x;
                                          }
                                      });

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function toJson($keySelectorOrOptions = null, int $options = 0, int $depth = 512) : IString {
        if (1 === \func_num_args()) {
            if ((null !== $keySelectorOrOptions) && !static::isCallable($keySelectorOrOptions)) {
                // swap values

                $options              = $keySelectorOrOptions;
                $keySelectorOrOptions = null;
            }
        }

        $keySelectorOrOptions = static::asCallable($keySelectorOrOptions);
        if (null === $keySelectorOrOptions) {
            // default
            $keySelectorOrOptions = true;
        }

        return new ClrString(\json_encode($this->toArray($keySelectorOrOptions),
                                          $options, $depth));
    }

    /**
     * Creates a closure from a lambda expression.
     *
     * @param string $expr The expression.
     * @param bool $throwException Throw exception or return (false) instead.
     *
     * @return \Closure|bool The closure or (false) an error
     *
     * @throws ArgumentException $expr is no valid expression.
     * @throws FormatException Seems to be a lambda expression, but has an invalid format.
     */
    public static function toLambda($expr, bool $throwException = true) {
        $expr = \trim($expr);

        // check for lambda
        if (1 === \preg_match("/^(\\s*)([\\(]?)([^\\)]*)([\\)]?)(\\s*)(=>)/m", $expr, $lambdaMatches)) {
            if ((empty($lambdaMatches[2]) && !empty($lambdaMatches[4])) ||
                (!empty($lambdaMatches[2]) && empty($lambdaMatches[4])))
            {
                if ($throwException) {
                    throw new FormatException();
                }

                return false;
            }

            $lambdaBody = \trim(\substr($expr, \strlen($lambdaMatches[0])));

            while ((\strlen($lambdaBody) >= 2) &&
                   ('{' === \substr($lambdaBody, 0, 1)) && ('}' === \substr($lambdaBody, -1))) {

                $lambdaBody = \trim(\substr($lambdaBody, 1, \strlen($lambdaBody) - 2));
            }

            if ((';' !== \substr($lambdaBody, -1))) {
                $lambdaBody = \sprintf('return %s;',
                                       $lambdaBody);
            }

            if ('' === $lambdaBody) {
                $lambdaBody = 'return null;';
            }

            return eval(\sprintf('return function(%s) { %s };',
                                 $lambdaMatches[3], $lambdaBody));
        }

        if ($throwException) {
            throw new ArgumentException('expr');
        }

        return false;
    }

    /**
     * Creates a reflector object for a function.
     *
     * @param mixed $func The function.
     *
     * @return \ReflectionFunctionAbstract The created reflector.
     */
    protected static function toReflectionFunction($func) : \ReflectionFunctionAbstract {
        if (\is_object($func)) {
            if (\method_exists($func, '__invoke')) {
                $func = array($func, '__invoke');
            }
        }

        if (\is_array($func)) {
            return new \ReflectionMethod($func[0], $func[1]);
        }

        return new \ReflectionFunction($func);
    }

    /**
     * {@inheritDoc}
     */
    public final function union($second, $equalityComparer = null) : IEnumerable {
        return $this->concat($second)
                    ->distinct($equalityComparer);
    }

    /**
     * Updates the arguments of "order" based methods.
     *
     * @param int $argCount The number of submitted arguments.
     * @param int $mustBe The required number of arguments to update values.
     * @param mixed &$comparer The comparer.
     * @param mixed &$preventKeys The value that indicates if keys should be prevented or not.
     */
    protected static function updateOrderArguments(int $argCount, int $mustBe, &$comparer, &$preventKeys) {
        if ($mustBe === $argCount) {
            if (\is_bool($comparer)) {
                // swap values

                $preventKeys = $comparer;
                $comparer    = null;
            }
        }
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
    protected static function updatePredicateAndDefaultValue(int $argCount, &$predicate, &$defValue) {
        if (static::isCallable($predicate)) {
            return;
        }

        if (1 !== $argCount) {
            return;
        }

        // use $predicate as default value
        $defValue  = $predicate;
        $predicate = null;
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
    public final function where($predicate) : IEnumerable {
        if (null === $predicate) {
            throw new ArgumentNullException('predicate');
        }

        return static::createEnumerable($this->whereInner(static::asCallable($predicate)));
    }

    /**
     * @see EnumerableBase::where()
     */
    protected function whereInner(callable $predicate) {
        $predicate = static::wrapPredicate($predicate);

        $index   = 0;
        $prevVal = null;
        $value   = null;
        while ($this->valid()) {
            $ctx = new EachItemContext($this, $index++, true, $prevVal);
            $ctx->value($value);

            if ($predicate($ctx->item(), $ctx)) {
                yield $ctx->item();
            }

            if ($ctx->cancel()) {
                break;
            }

            $prevVal = $ctx->nextValue();
            $value   = $ctx->value();
        }
    }

    /**
     * Wraps a predicate with a callable that requires a boolean as result value.
     *
     * @param callable $predicate The predicate to wrap.
     *
     * @return callable The wrapper.
     */
    public static function wrapPredicate(callable $predicate) : callable {
        return function($x, $ctx) use ($predicate) : bool {
                   return $predicate($x, $ctx);
               };
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($str) {
        $this->__construct(static::asIterator(\unserialize($str), true));
    }

    /**
     * {@inheritDoc}
     */
    public final function zip($second, $selector) : IEnumerable {
        if (null === $selector) {
            throw new ArgumentNullException('selector');
        }

        return static::createEnumerable($this->zipInner($second,
                                                        static::asCallable($selector)));
    }

    /**
     * @see EnumerableBase::zip()
     */
    protected function zipInner($second, callable $selector) {
        if (!$second instanceof IEnumerable) {
            $second = static::createEnumerable($second);
        }

        $index    = 0;
        $prevVal1 = null;
        $prevVal2 = null;
        $value1   = null;
        $value2   = null;
        while ($this->valid() && $second->valid()) {
            $ctx1 = new EachItemContext($this, $index, true, $prevVal1);
            $ctx1->value($value1);

            $ctx2 = new EachItemContext($second, $index, true, $prevVal2);
            $ctx2->value($value2);

            ++$index;

            $zipped = $selector($ctx1->item(), $ctx2->item(), $ctx1, $ctx2);

            if ($ctx1->cancel() || $ctx2->cancel()) {
                break;
            }

            yield $zipped;

            $prevVal1 = $ctx1->nextValue();
            $prevVal2 = $ctx2->nextValue();

            $value1 = $ctx1->value();
            $value2 = $ctx2->value();
        }
    }
}
