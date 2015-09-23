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
use \System\ArgumentOutOfRangeException;
use \System\Collections\EnumerableBase;


/**
 * A common sequence.
 *
 * @package System\Linq
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class Enumerable extends EnumerableBase {
    /**
     * Builds a sequence with a specific number of random values.
     *
     * @param int $count The number of items to create.
     * @param int|callable $maxOrSeeder The exclusive maximum value (mt_getrandmax() - 1 by default).
     *                                  If there are only two arguments and that value is a callable
     *                                  it is set to (null) and its origin value is written to $seeder.
     * @param int $min The inclusive minimum value (0 by default).
     * @param callable $seeder The optional function that initializes the random
     *                         number generator.
     *
     * @return static
     *
     * @throws ArgumentException $seeder is no valid callable / lambda expression.
     * @throws ArgumentOutOfRangeException $count is less than 0.
     */
    public final static function buildRandom(int $count, $maxOrSeeder = null, int $min = 0, $seeder = null) {
        if ($count < 0) {
            throw new ArgumentOutOfRangeException('count', $count);
        }

        if (2 === \func_num_args()) {
            if (static::isCallable($maxOrSeeder)) {
                $seeder      = $maxOrSeeder;
                $maxOrSeeder = null;
            }
        }

        $seeder = static::asCallable($seeder);

        if (null === $maxOrSeeder) {
            $maxOrSeeder = \mt_getrandmax();
        }

        if (null !== $seeder) {
            $seeder();
        }

        return static::create(static::buildRandomInner($count, $min, $maxOrSeeder));
    }

    /**
     * @see Enumerable::buildRandom()
     */
    protected static function buildRandomInner(int $count, int $min, int $max) {
        for ($i = 0; $i < $count; $i++) {
            yield \mt_rand($min, $max - 1);
        }
    }

    /**
     * Creates a new instance from an item list.
     *
     * @param mixed $items The initial values.
     *
     * @return static
     */
    public static function create($items = null) {
        return new static(static::asIterator($items, true));
    }

    /**
     * {@inheritDoc}
     */
    protected static function createEnumerable($items = null) {
        return new self(static::asIterator($items, true));
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
}
