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


use \System\Collections\Dictionary;
use \System\Collections\EnumerableBase;
use \System\Collections\IDictionary;
use \System\Collections\IEnumerable;


/**
 * A lookup object.
 *
 * @package System\Linq
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
final class Lookup extends EnumerableBase implements ILookup {
    /**
     * Initializes a new instance of that class.
     *
     * @param IEnumerable $grps The sequence of groupings.
     *
     * @throws \System\ArgumentException
     */
    public function __construct($grps) {
        $dict = $grps;

        if (!$grps instanceof IDictionary) {
            $grps = static::asIterator($grps, true);

            $dict = new Dictionary();
            while ($grps->valid()) {
                $g = $grps->current();
                if (!$g instanceof IGrouping) {
                    throw new \System\ArgumentException('grps',
                                                        'Sequence contains at least one item that is no grouping!');
                }

                $dict->add($g->key(), $g);

                $grps->next();
            }
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
    public function current() {
        return $this->_i->current()
                        ->value();
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($key) {
        return $this->_i->offsetExists($key);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($key) {
        if ($this->offsetExists($key)) {
            return $this->_i->offsetGet($key)
                            ->getIterator();
        }

        $this->throwException('Key not found!');
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($key, $value) {
        $this->_i->offsetSet($key, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($key) {
        $this->_i->offsetUnset($key);
    }
}
