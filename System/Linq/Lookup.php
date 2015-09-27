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


namespace System\Linq;

use \System\InvalidOperationException;
use \System\Collections\Dictionary;
use \System\Collections\IEnumerable;


/**
 * A lookup object.
 *
 * @package System\Linq
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class Lookup extends Enumerable implements ILookup {
    /**
     * Initializes a new instance of that class.
     *
     * @param IEnumerable $grps The sequence of groupings.
     *
     * @throws \System\ArgumentException
     */
    public function __construct($grps) {
        $grps = static::asIterator($grps, true);

        $dict = new Dictionary(null, null, null,
                               \sprintf('$x => $x instanceof %s', IGrouping::class));

        while ($grps->valid()) {
            /* @var IGrouping $curGrouping */
            $curGrouping = $grps->current();

            $dict->add($curGrouping->key(), $curGrouping);

            $grps->next();
        }

        parent::__construct($dict);
    }

    /**
     * {@inheritDoc}
     */
    public function containsKey($key) {
        return $this->offsetExists($key);
    }

    /**
     * {@inheritDoc}
     */
    public function count() {
        return $this->_i
                    ->count();
    }

    /**
     * {@inheritDoc}
     */
    public function current() {
        return $this->_i
                    ->current()
                    ->value();
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($key) {
        return $this->_i
                    ->offsetExists($key);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($key) {
        return $this->_i[$key]
                    ->getIterator();
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($key, $value) {
        throw new InvalidOperationException();
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($key) {
        throw new InvalidOperationException();
    }
}
