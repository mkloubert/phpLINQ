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

namespace System\Linq;

use \System\ArgumentException;
use \System\ArgumentNullException;
use \System\ArgumentOutOfRangeException;
use \System\Collections\Collection;
use \System\Collections\Dictionary;
use \System\Collections\EnumerableException;
use \System\Collections\IDictionary;
use \System\Collections\IEachItemContext;
use \System\Collections\IIndexedItemContext;
use \System\Collections\IItemContext;
use \System\Collections\IList;
use \System\Collections\ISet;
use \System\Collections\EachItemContext;
use \System\Collections\ElementNotFoundException;
use \System\Collections\IEnumerable;
use \System\Collections\KeySelectorIterator;
use \System\Collections\KeyAndValueSelectorIterator;
use \System\Collections\Set;
use \System\ClrString;
use \System\IString;
use \System\Object;


/**
 * A sequence.
 *
 * @package System\Collections
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class Enumerable extends Object implements IEnumerable {
    use \System\Linq\Traits\Enumerable\Factories;


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
    public final function appendToArray(&$arr, bool $withKeys = false) : IEnumerable {
        static::throwIfNoValidArray($arr);

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
     * {@inheritDoc}
     */
    public function asEnumerable() : IEnumerable {
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function asResettable() : IEnumerable {
        if ($this->_i instanceof IEnumerable) {
            return $this->_i->asResettable();
        }

        if ($this->_i instanceof \Generator) {
            return static::createEnumerable($this->toArray(true));
        }

        if ($this->_i instanceof KeySelectorIterator) {
            $newIterator = $this->_i
                                ->createNewFromSequence($this->_i
                                                             ->sequence()
                                                             ->asResettable());

            return static::createEnumerable($newIterator);
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
     * @see Enumerable::concat()
     */
    protected function concatInner(array $itemLists) {
        // first the values of that sequence
        while ($this->valid()) {
            yield $this->key() => $this->current();
            $this->next();
        }

        // now the items from the lists
        foreach ($itemLists as $items) {
            $iterator = static::asIterator($items);
            if (null === $iterator) {
                continue;
            }

            while ($iterator->valid()) {
                yield $iterator->key() => $iterator->current();
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
        return $this->iterateWithItemContext(function($x, IEachItemContext $ctx) {
                                                 $ctx->result($ctx->index() + 1);
                                             }, 0);
    }

    /**
     * Creates a new instance from an item list.
     *
     * @param mixed $items The initial values.
     *
     * @return static
     */
    public static function create($items = null) {
        return new self(static::asIterator($items, true));
    }

    /**
     * {@inheritDoc}
     */
    public final static function createEnumerable($items = null) : IEnumerable {
        return new self(static::asIterator($items, true));
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
    public final function defaultArrayIfEmpty($items = null) : IEnumerable {
        if ($this->isEmpty()) {
            return static::createEnumerable($items);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public final function defaultIfEmpty() : IEnumerable {
        return $this->defaultArrayIfEmpty(\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public final function distinct($equalityComparer = null) : IEnumerable  {
        return static::createEnumerable($this->distinctInner(static::getEqualityComparerSafe($equalityComparer)));
    }

    /**
     * @see Enumerable::distinct()
     */
    protected function distinctInner(callable $equalityComparer) {
        $temp = [];

        while ($this->valid()) {
            $curItem = $this->current();

            // search for duplicate
            $alreadyInList = false;
            foreach ($temp as $existingItem) {
                if (!$equalityComparer($curItem, $existingItem)) {
                    continue;
                }

                // found duplicate
                $alreadyInList = true;
                break;
            }

            if (!$alreadyInList) {
                yield $this->key() => $curItem;
                $temp[] = $curItem;
            }

            $this->next();
        }
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
    public final function elementAt(int $index) {
        $result = $this->elementAtOrDefault($index, null, $found);

        if (!$found) {
            throw new ElementNotFoundException();
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function elementAtOrDefault(int $index, $defValue = null, &$found = false) {
        return $this->skip($index)
                    ->firstOrDefault(null, $defValue, $found);
    }

    /**
     * {@inheritDoc}
     */
    public final function except($second, $equalityComparer = null) : IEnumerable {
        $equalityComparer = static::getEqualityComparerSafe($equalityComparer);

        return static::createEnumerable($this->exceptInner($second, $equalityComparer));
    }

    /**
     * @see Enumerable::except()
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
                if (!$equalityComparer($curItem, $ite)) {
                    continue;
                }

                $found = true;
                break;
            }

            if (!$found) {
                yield $this->key() => $curItem;
            }

            $this->next();
        }
    }

    /**
     * {@inheritDoc}
     */
    public final function first($predicate = null) {
        $result = $this->firstOrDefault($predicate, null, $found);

        if (!$found) {
            throw new ElementNotFoundException();
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public final function firstOrDefault($predicateOrDefValue = null, $defValue = null, &$found = false) {
        static::updatePredicateAndDefaultValue(\func_num_args(),
                                               $predicateOrDefValue, $defValue);

        $predicateOrDefValue = static::getPredicateSafe($predicateOrDefValue);

        return $this->iterateWithItemContext(function($x, IEachItemContext $ctx) use (&$found, $predicateOrDefValue) {
                                                 if (!$predicateOrDefValue($x, $ctx)) {
                                                     return;
                                                 }

                                                 $found = true;

                                                 $ctx->result($x);
                                                 $ctx->cancel(true);
                                             }, $defValue);
    }

    /**
     * Creates a new sequence from a JSON string.
     *
     * @param mixed $json The JSON data.
     *
     * @return static
     */
    public static function fromJson($json) {
        return static::create(\json_decode($json, true));
    }

    /**
     * Creates a new instance from a list of values.
     *
     * @param mixed ...$value The initial values.
     *
     * @return static
     */
    public static function fromValues() {
        return static::create(\func_get_args());
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

        $createSeq = $this->getType()
                          ->getMethod('createEnumerable')->getClosure(null);

        return $createSeq($groups)->select(function(\stdClass $x) use ($createSeq) : IGrouping {
                                               return new Grouping($x->key,
                                                                   $createSeq($x->values));
                                           })
                                  ->withNewKeys('($key, $item) => $item->key()');
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
     * @see Enumerable::groupJoin()
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
     * @see Enumerable::intersect()
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
                if (!$equalityComparer($curItem, $v)) {
                    // not found
                    continue;
                }

                unset($second[$k]);
                yield $this->key() => $curItem;

                break;
            }

            $this->next();
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
     * @see Enumerable::join()
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
     * Iterates over that sequence by using an item context.
     *
     * @param callable $action The action to invoke for each item.
     * @param mixed $initialResult The initial result value.
     * @param mixed $initialValue The initial iteration value.
     *
     * @return mixed The result of the iteration.
     */
    protected function iterateWithItemContext($action, $initialResult = null, $initialValue = null) {
        $action = static::asCallable($action);

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
    public final function last($predicate = null) {
        $result = $this->lastOrDefault($predicate, null, $found);

        if (!$found) {
            throw new ElementNotFoundException();
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public final function lastOrDefault($predicateOrDefValue = null, $defValue = null, &$found = false) {
        static::updatePredicateAndDefaultValue(\func_num_args(),
                                               $predicateOrDefValue, $defValue);

        $predicateOrDefValue = static::getPredicateSafe($predicateOrDefValue);

        return $this->iterateWithItemContext(function($x, IEachItemContext $ctx) use (&$found, $predicateOrDefValue) {
                                                 if (!$predicateOrDefValue($x, $ctx)) {
                                                     return;
                                                 }

                                                 $found = true;
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
        if ($type instanceof \ReflectionClass) {
            $type = $type->getName();
        }
        else {
            $type = \trim($type);
        }

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

                                    case 'numeric':
                                        return \is_numeric($x);

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
    public final function order($comparer = null) : IOrderedEnumerable {
        return $this->orderBy(true, $comparer);
    }

    /**
     * {@inheritDoc}
     */
    public function orderBy($selector, $comparer = null) : IOrderedEnumerable {
        if (true === $selector) {
            $selector = function($x) {
                return $x;
            };
        }

        return new OrderedEnumerable($this,
                                     static::asCallable($selector),
                                     static::getComparerSafe($comparer));
    }

    /**
     * {@inheritDoc}
     */
    public final function orderByDescending($selector, $comparer = null) : IOrderedEnumerable {
        $comparer = static::getComparerSafe($comparer);

        return $this->orderBy($selector,
                              function($x, $y) use ($comparer) : int {
                                  return $comparer($y, $x);
                              });
    }

    /**
     * {@inheritDoc}
     */
    public final function orderDescending($comparer = null) : IOrderedEnumerable {
        return $this->orderByDescending(true, $comparer);
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
        $randProvider = null
    ) : IOrderedEnumerable {

        if (true === $seeder) {
            $seeder = $this->getType()
                           ->getMethod('seedRandom')->getClosure(null);
        }

        $seeder       = static::asCallable($seeder);
        $randProvider = static::asCallable($randProvider);

        if (null === $randProvider) {
            $randProvider = function () {
                return \mt_rand();
            };
        }

        if (null !== $seeder) {
            $seeder();
        }

        return $this->orderBy($randProvider, null);
    }

    /**
     * Creates a sequence with a range of numbers.
     *
     * @param number $start The start value.
     * @param int $count The number of items.
     * @param number|callable $increaseBy The increase value or the function that provides that value.
     *
     * @return IEnumerable The new sequence.
     *
     * @throws ArgumentOutOfRangeException $count is less than 0.
     */
    public final static function range($start, int $count, $increaseBy = 1) : IEnumerable {
        if ($count < 0) {
            throw new ArgumentOutOfRangeException($count, 'count');
        }

        $increaseByFunc = $increaseBy;
        if (!static::isCallable($increaseByFunc)) {
            $increaseByFunc = function() use ($increaseBy) {
                return $increaseBy;
            };
        }
        else {
            $increaseByFunc = static::asCallable($increaseByFunc);
        }

        return static::createEnumerable(static::rangeInner($start, $count, $increaseByFunc));
    }

    /**
     * @see Enumerable::range()
     */
    protected static function rangeInner($start, int $count, callable $increaseByFunc) {
        $result = $start;

        for ($i = 0; $i < $count; $i++) {
            $currentValue = $result;

            yield $result;

            $result += $increaseByFunc($currentValue, $i);
        }
    }

    /**
     * {@inheritDoc}
     */
    public final function reset() : IEnumerable {
        $this->resetInner();
        return $this;
    }

    /**
     * Enumerable::reset()
     */
    protected function resetInner() {
        $this->_i->rewind();
    }

    /**
     * {@inheritDoc}
     */
    public final function reverse() : IOrderedEnumerable {
        return $this->orderBy(function($x, IIndexedItemContext $ctx) {
                                  return \PHP_INT_MAX - $ctx->index();
                              },
                              null);
    }

    /**
     * {@inheritDoc}
     */
    public final function rewind() {
        // deactivated
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
     * @see Enumerable::select()
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

            yield $ctx->key() => $newItem;

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
     * @see Enumerable::selectMany()
     */
    protected function selectManyInner(callable $selector) {
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
    public final function sequenceEqual($other, $equalityComparer = null, $keyEqualityComparer = null) : bool {
        $equalityComparer = static::getEqualityComparerSafe($equalityComparer);

        if (null !== $keyEqualityComparer) {
            $keyEqualityComparer = static::getEqualityComparerSafe($keyEqualityComparer);
        }

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

            if (null !== $keyEqualityComparer) {
                if (!$keyEqualityComparer($this->key(), $other->key())) {
                    // not same keys
                    return false;
                }
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
    public final function single($predicate = null) {
        $result = $this->singleOrDefault($predicate, null, $found);

        if (!$found) {
            throw new ElementNotFoundException();
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public final function singleOrDefault($predicateOrDefValue = null, $defValue = null, &$found = false) {
        static::updatePredicateAndDefaultValue(\func_num_args(),
                                               $predicateOrDefValue, $defValue);

        $predicateOrDefValue = static::getPredicateSafe($predicateOrDefValue);

        $me = $this;

        return $this->iterateWithItemContext(function($x, IEachItemContext $ctx) use (&$found, $me, $predicateOrDefValue) {
                                                 if (!$predicateOrDefValue($x, $ctx)) {
                                                     return;
                                                 }

                                                 if (true === $ctx->value()) {
                                                     $te = $me->getType()
                                                              ->getMethod('throwException')
                                                              ->getClosure($me);

                                                     $te('Sequence contains more than one matching element!');
                                                 }

                                                 $found = true;

                                                 $ctx->result($x);
                                                 $ctx->value(true);
                                             }, $defValue);
    }

    /**
     * {@inheritDoc}
     */
    public final function skip(int $count) : IEnumerable {
        if ($count < 0) {
            throw new ArgumentOutOfRangeException($count, 'count');
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
            throw new ArgumentOutOfRangeException($count, 'count');
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
     * @see Enumerable::takeWhile()
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
                yield $ctx->key() => $ctx->item();
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
     * Throws an exception if a value is no valid array type.
     *
     * @param mixed $arr The value to check.
     *
     * @throws ArgumentException No valid array type.
     * @throws ArgumentNullException $arr is (null).
     */
    protected static function throwIfNoValidArray($arr) {
        if (null === $arr) {
            throw new ArgumentNullException('arr');
        }

        if (\is_array($arr) || ($arr instanceof \ArrayAccess)) {
            return;
        }

        throw new ArgumentException('arr');
    }

    /**
     * {@inheritDoc}
     */
    public function toArray($keySelector = null) : array {
        if (true === $keySelector) {
            $keySelector = function($key) {
                return $key;
            };
        }

        $keySelector = static::asCallable($keySelector);

        return $this->iterateWithItemContext(function($x, IEachItemContext $ctx) use ($keySelector) {
                                                 $result = $ctx->result();

                                                 if (null === $keySelector) {
                                                     // autokey
                                                     $result[] = $x;
                                                 }
                                                 else {
                                                     $result[$keySelector($ctx->key(), $x, $ctx)] = $x;
                                                 }

                                                 $ctx->result($result);
                                             }, []);
    }

    /**
     * {@inheritDoc}
     */
    public final function toDictionary($keyComparer = null, $keyValidator = null, $valueValidator = null) : IDictionary {
        return new Dictionary($this, $keyComparer, $keyValidator, $valueValidator);
    }

    /**
     * {@inheritDoc}
     */
    public final function toJson($keySelectorOrOptions = null, int $options = 0, int $depth = 512) : IString {
        if (1 === \func_num_args()) {
            if ((null !== $keySelectorOrOptions) && !static::isCallable($keySelectorOrOptions)) {
                // swap values

                $options              = $keySelectorOrOptions;
                $keySelectorOrOptions = null;
            }
        }
        else if (2 === \func_num_args()) {
            if ((null !== $keySelectorOrOptions) && !static::isCallable($keySelectorOrOptions)) {
                $depth                = $options;
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
     * {@inheritDoc}
     */
    public final function toList($equalityComparer = null, $itemValidator = null) : IList {
        return new Collection($this, $equalityComparer, $itemValidator);
    }

    /**
     * {@inheritDoc}
     */
    public final function toLookup(
        $keySelector,
        $keyEqualityComparer = null,
        $elementSelector = null
    ) : ILookup {

        $elementSelector = static::asCallable($elementSelector);

        $grps = $this->groupBy($keySelector, $keyEqualityComparer);
        if (null !== $elementSelector) {
            $grps = $grps->select(function(IGrouping $g) use ($elementSelector) : IGrouping {
                                      return new Grouping($g->key(),
                                                          $g->getIterator()->select($elementSelector));
                                  });
        }

        return new Lookup($grps);
    }

    /**
     * {@inheritDoc}
     */
    public final function toSet($equalityComparer = null, $itemValidator = null) : ISet {
        return new Set($this, $equalityComparer, $itemValidator);
    }

    /**
     * {@inheritDoc}
     */
    public final function union($second, $equalityComparer = null) : IEnumerable {
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
     * @see Enumerable::where()
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
                yield $ctx->key() => $ctx->item();
            }

            if ($ctx->cancel()) {
                break;
            }

            $prevVal = $ctx->nextValue();
            $value   = $ctx->value();
        }
    }

    /**
     * {@inheritDoc}
     */
    public final function withNewKeys($keySelector) : IEnumerable {
        return static::createEnumerable(new KeySelectorIterator($this, $keySelector));
    }

    /**
     * {@inheritDoc}
     */
    public final function withNewKeysAndValues($keySelector, $valueSelector) : IEnumerable {
        return static::createEnumerable(new KeyAndValueSelectorIterator($this, $keySelector, $valueSelector));
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
     * @see Enumerable::zip()
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
