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
use \System\ArgumentOutOfRangeException;
use \System\InvalidOperationException;


/**
 * A dictionary / hashtable.
 *
 * @package System\Collections
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class Dictionary extends ArrayCollectionBase implements IDictionary {
    private $_keyEqualityComparer;
    private $_keyValidator;
    private $_valueValidator;

    /**
     * Initializes a new instance of that class.
     *
     * @param mixed $items The initial items.
     * @param callable $keyEqualityComparer The optional key equality comparer.
     * @param callable $keyValidator The custom validator for the keys to use.
     * @param callable $valueValidator The custom validator for the values to use.
     */
    public function __construct($items = null, $keyEqualityComparer = null, $keyValidator = null, $valueValidator = null) {
        $items                      = static::asIterator($items, true);
        $this->_keyEqualityComparer = static::getEqualityComparerSafe($keyEqualityComparer);
        $this->_keyValidator        = static::getValueValidatorSafe($keyValidator);
        $this->_valueValidator      = static::getValueValidatorSafe($valueValidator);

        $this->clearInner();

        while ($items->valid()) {
            $key = $items->key();
            $this->throwIfKeyIsInvalid($key);

            $value = $items->current();
            $this->throwIfValueIsInvalid($value);

            $this->addInner($key, $value);

            $items->next();
        }

        $this->reset();
    }


    /**
     * {@inheritDoc}
     */
    public final function add($key, $value) {
        $this->throwIfReadOnly();

        $this->throwIfKeyIsInvalid($key);
        $this->throwIfValueIsInvalid($value);

        $this->addInner($key, $value);
    }

    /**
     * @see Dictionary::add()
     */
    protected function addInner($key, $value) {
        if ($this->containsKey($key)) {
            throw new ArgumentException('key', 'Key already exists.');
        }

        $this->_items[] = static::makeObject($key, $value);
    }

    /**
     * {@inheritDoc}
     */
    public final function appendToArray(array &$arr, bool $withKeys = false) : IEnumerable {
        return $this->iterateWithItemContext(function(IDictionaryEntry $x) use (&$arr, $withKeys) {
            if (!$withKeys) {
                $arr[] = $x->value();
            }
            else {
                $arr[$x->key()] = $x->value();
            }
        }, $this);
    }

    /**
     * {@inheritDoc}
     */
    public final function asEnumerable() : IEnumerable {
        $items = [];
        foreach ($this->_items as $i) {
            $items[] = static::objectToEntry($i);
        }

        return static::createEnumerable(new DictionaryEntryIterator(new \ArrayIterator($items)));
    }

    /**
     * {@inheritDoc}
     */
    public final function clear() {
        $this->throwIfReadOnly();

        $this->clearInner();
    }

    /**
     * @see Dictionary::clear()
     */
    protected function clearInner() {
        $this->_items = [];
    }

    private function compareKeys($x, $y) {
        return \call_user_func($this->_keyEqualityComparer,
                               $x, $y);
    }

    /**
     * {@inheritDoc}
     */
    public final function containsKey($key) : bool {
        $this->throwIfKeyIsInvalid($key);

        foreach ($this->keys() as $dictKey) {
            if ($this->compareKeys($dictKey, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public final function current() {
        if (!$this->valid()) {
            return null;
        }

        return static::objectToEntry(parent::current());
    }

    /**
     * {@inheritDoc}
     */
    public final function elementAtOrDefault(int $index, $defValue = null, &$found = false) {
        if (\array_key_exists($index, $this->_items)) {
            $found = true;
            return self::objectToEntry($this->_items[$index]);
        }

        return $defValue;
    }

    private function indexOfByOffset($offset) {
        foreach ($this->_items as $index => $item) {
            if ($this->compareKeys($item->key, $offset)) {
                return $index;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isFixedSize() : bool {
        return $this->isReadOnly();
    }

    private function isKeyValid($key) : bool {
        return \call_user_func($this->_keyValidator,
                               $key);
    }

    /**
     * {@inheritDoc}
     */
    public function isReadOnly() : bool {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isSynchronized() : bool {
        return false;
    }

    private function isValueValid($key) : bool {
        return \call_user_func($this->_valueValidator,
                               $key);
    }

    /**
     * {@inheritDoc}
     */
    public final function key() {
        return $this->valid() ? $this->current()->key()
                              : $this->getEOFKey();
    }

    /**
     * {@inheritDoc}
     */
    public final function keys() : IEnumerable {
        return static::createEnumerable($this->_items)
                     ->select('$x => $x->key');
    }

    private static function makeObject($key, $value) {
        $result        = new \stdClass();
        $result->key   = $key;
        $result->value = $value;

        return $result;
    }

    private static function objectToEntry(\stdClass $obj) : IDictionaryEntry {
        return new DictionaryEntry($obj->key,
                                   $obj->value);
    }

    /**
     * {@inheritDoc}
     */
    public final function offsetExists($offset) {
        $this->throwIfKeyIsInvalid($offset);

        return $this->containsKey($offset);
    }

    /**
     * {@inheritDoc}
     */
    public final function offsetGet($offset) {
        $this->throwIfKeyIsInvalid($offset);

        $i = $this->indexOfByOffset($offset);
        if (false !== $i) {
            return $this->_items[$i]->value;
        }

        $this->throwKeyOutOfRangeException($offset);
    }

    /**
     * {@inheritDoc}
     */
    public final function offsetSet($offset, $value) {
        $this->throwIfKeyIsInvalid($offset);

        $this->throwIfReadOnly();

        $doAdd = false;
        if (null === $offset) {
            $doAdd = true;

            // find next index
            $offset = $this->count();
            while ($this->containsKey($offset)) {
                ++$offset;
            }
        }

        $i = $this->indexOfByOffset($offset);
        if (!$doAdd && (false !== $i)) {
            $this->_items[$i]->value = $value;
        }
        else {
            $this->add($offset, $value);
        }
    }

    /**
     * {@inheritDoc}
     */
    public final function offsetUnset($offset) {
        $this->throwIfKeyIsInvalid($offset);

        $this->removeKey($offset);
    }

    /**
     * {@inheritDoc}
     */
    public final function remove($key) : bool {
        return $this->removeKey($key);
    }

    /**
     * {@inheritDoc}
     */
    public final function removeKey($key) : bool {
        $this->throwIfKeyIsInvalid($key);

        $this->throwIfReadOnly();

        return $this->removeKeyInner($key);
    }

    /**
     * @see Dictionary::removeKey()
     */
    protected function removeKeyInner($key) : bool {
        $i = $this->indexOfByOffset($key);
        if (false !== $i) {
            \array_splice($this->_items, $i, 1);
            return true;
        }

        return false;
    }

    /**
     * Throws an exception if a key is invalid.
     *
     * @param mixed $key The key to check.
     *
     * @throws ArgumentException Is invalid key.
     */
    protected final function throwIfKeyIsInvalid($key) {
        if (!$this->isKeyValid($key)) {
            throw new ArgumentException('key', 'Key is not valid!');
        }
    }

    /**
     * Throws an exception if a value is invalid.
     *
     * @param mixed $value The value to check.
     *
     * @throws ArgumentException Is invalid value.
     */
    protected final function throwIfValueIsInvalid($value) {
        if (!$this->isValueValid($value)) {
            throw new ArgumentException('value', 'Value is not valid!');
        }
    }

    /**
     * Throws an exception if that dictionary is read-only.
     *
     * @throws InvalidOperationException Dictionary is read-only.
     */
    protected final function throwIfReadOnly() {
        if ($this->isReadOnly()) {
            throw new InvalidOperationException('Dictionary is read only!');
        }
    }

    private function throwKeyOutOfRangeException($key) {
        throw new ArgumentOutOfRangeException('key', $key, 'Key not found!');
    }

    /**
     * {@inheritDoc}
     */
    public final function values() : IEnumerable {
        return static::createEnumerable($this->_items)
                     ->select('$x => $x->value');
    }
}
