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
 * A common set.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 * @package System\Collections
 */
final class Set extends ArrayCollectionBase implements ISet {
    private $_equalityComparer;


    /**
     * Initializes a new instance of that class.
     *
     * @param mixed $items The initial items.
     * @param callable $equalityComparer The custom item comparer to use.
     */
    public function __construct($items = null, $equalityComparer = null) {
        $this->_equalityComparer = static::getEqualityComparerSafe($equalityComparer);

        $this->clear();

        $i = static::asIterator($items, true);
        while ($i->valid()) {
            $this->add($i->current());

            $i->next();
        }

        $this->reset();
    }


    /**
     * {@inheritDoc}
     */
    public function add($item) : bool {
        if (!$this->containsItem($item)) {
            $this->_items[] = $item;
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function clear() {
        $this->_items = array();
    }

    private function compareItems($x, $y) {
        return \call_user_func($this->_equalityComparer,
                               $x, $y);
    }

    /**
     * {@inheritDoc}
     */
    public function containsItem($item) : bool {
        foreach ($this->_items as $i) {
            if ($this->compareItems($item, $i)) {
                // found
                return true;
            }
        }

        // not found
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isReadOnly() {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function remove($item) : bool {
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
}
