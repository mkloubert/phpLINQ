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


namespace System;


/**
 * Wraps an object or value for sort operations.
 *
 * @package System
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
final class Comparable extends ObjectWrapper implements IComparable {
    /**
     * {@inheritDoc}
     */
    public function compareTo($other) {
        if ($other instanceof IObject) {
            if ($other->equals($this)) {
                // equal
                return 0;
            }
        }

        if ($this->getWrappedValue() > $other) {
            // greater
            return 1;
        }

        if ($this->getWrappedValue() < $other) {
            // smaller
            return -1;
        }

        // equal
        return 0;
    }
}