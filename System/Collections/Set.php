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

use \System\Collections\ObjectModel\ReadOnlySet;


/**
 * A common set.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 * @package System\Collections
 */
class Set extends ArrayCollectionBase implements ISet {
    private $_equalityComparer;
    private $_itemValidator;


    /**
     * Initializes a new instance of that class.
     *
     * @param mixed $items The initial items.
     * @param callable $equalityComparer The custom item comparer to use.
     * @param callable $itemValidator The custom item validator to use.
     */
    public function __construct($items = null, $equalityComparer = null, $itemValidator = null) {
        $items                   = static::asIterator($items, true);
        $this->_equalityComparer = static::getEqualityComparerSafe($equalityComparer);
        $this->_itemValidator    = static::getValueValidatorSafe($itemValidator);

        $this->clearInner();

        while ($items->valid()) {
            $i = $items->current();
            $this->throwIfItemIsInvalid($i);

            $this->addInner($i);

            $items->next();
        }

        $this->reset();
    }


    /**
     * {@inheritDoc}
     */
    public final function add($item) : bool {
        $this->throwIfFixedSize();
        $this->throwIfItemIsInvalid($item);

        return $this->addInner($item);
    }

    /**
     * @see Set::add()
     */
    protected function addInner($item) : bool {
        if (!$this->containsItem($item)) {
            $this->_items[] = $item;
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public final function asReadOnly() : IReadOnlySet {
        return !$this->isReadOnly() ? new ReadOnlySet($this->_items,
                                                      $this->_equalityComparer,
                                                      $this->_itemValidator)
                                    : $this;
    }

    /**
     * {@inheritDoc}
     */
    public final function clear() {
        $this->throwIfFixedSize();

        $this->clearInner();
    }

    /**
     * @see Set::clear()
     */
    protected function clearInner() {
        $this->_items = [];
    }

    private function compareItems($x, $y) {
        return \call_user_func($this->_equalityComparer,
                               $x, $y);
    }

    /**
     * {@inheritDoc}
     */
    public function containsItem($item) : bool {
        foreach (\func_get_args() as $itemToCheck) {
            $this->throwIfItemIsInvalid($itemToCheck);

            $found = false;
            foreach ($this->_items as $i) {
                if ($this->compareItems($itemToCheck, $i)) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                // at least one item was not found
                return false;
            }
        }

        return true;
    }

    private function isItemValid($item) : bool {
        return \call_user_func($this->_itemValidator,
                               $item);
    }

    /**
     * {@inheritDoc}
     */
    public final function remove($item) : bool {
        $this->throwIfFixedSize();
        $this->throwIfItemIsInvalid($item);

        return $this->removeInner($item);
    }

    /**
     * @ss Set::remove()
     */
    protected function removeInner($item) : bool {
        foreach ($this->_items as $index => $value) {
            if ($this->compareItems($item, $value)) {
                // found

                \array_splice($this->_items, $index, 1);
                return true;
            }
        }

        // not found
        return false;
    }

    /**
     * Throws an exception if an item is invalid.
     *
     * @param mixed $item The item to check.
     *
     * @throws InvalidItemException Is invalid item.
     */
    protected final function throwIfItemIsInvalid($item) {
        if (!$this->isItemValid($item)) {
            throw new InvalidItemException($item, 'item');
        }
    }
}
