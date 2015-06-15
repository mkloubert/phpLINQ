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

    public function key() {
        return $this->_i->key();
    }

    public function next() {
        $this->_i->next();
    }

    public final function reset() {
        $this->rewind();
        return $this;
    }

    public function rewind() {
        $this->_i->rewind();
    }

    public function runtimeVersion() {
        return "5.5";
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

    public function serialize() {
        return $this->toJson();
    }

    public function toArray($keySelector = null) {
        if (is_null($keySelector)) {
            $keySelector = function ($key, $value, $index) {
                return null;
            };
        }

        $result = array();

        $index = 0;
        while ($this->valid()) {
            $ctx = static::createContextObject($this, $index++);

            $key   = call_user_func($keySelector,
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
