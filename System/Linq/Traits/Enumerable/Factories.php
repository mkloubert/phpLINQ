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

namespace System\Linq\Traits\Enumerable;

use \System\ArgumentException;
use \System\ArgumentOutOfRangeException;
use \System\ClrString;
use \System\IString;
use \System\Collections\IEnumerable;


/**
 * Factory methods for \System\Linq\Enumerable class.
 *
 * @package System\CollectionsSystem\Linq\Traits\Enumerable
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
trait Factories {
    /**
     * Builds a sequence with a specific number of random values.
     *
     * @param int $count The number of items to create.
     * @param int|callable|bool $maxOrSeeder The exclusive maximum value (mt_getrandmax() - 1 by default).
     *                                       If there are only two arguments and that value is a callable
     *                                       it is set to (null) and its origin value is written to $seeder.
     * @param int $min The inclusive minimum value (0 by default).
     * @param callable|bool $seeder The optional function that initializes the random number generator.
     *                              If this value is (true), a default logic will be used.
     *
     * @return IEnumerable The created sequence.
     *
     * @throws ArgumentException $seeder is no valid callable / lambda expression.
     * @throws ArgumentOutOfRangeException $count is less than 0.
     */
    public final static function buildRandom(int $count, $maxOrSeeder = null, int $min = 0, $seeder = null) : IEnumerable {
        if ($count < 0) {
            throw new ArgumentOutOfRangeException($count, 'count');
        }

        if (2 === \func_num_args()) {
            if ((true === $maxOrSeeder) || static::isCallable($maxOrSeeder)) {
                $seeder      = $maxOrSeeder;
                $maxOrSeeder = null;
            }
        }

        if (true === $seeder) {
            $rc = new \ReflectionClass(static::class);

            $seeder = $rc->getMethod('seedRandom')->getClosure(null);
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
        return new self(static::asIterator($items, true));
    }

    /**
     * {@inheritDoc}
     */
    public final static function createEnumerable($items = null) : IEnumerable {
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

    /**
     * Creates a random list of chars.
     *
     * @param int $count The number of chars to return.
     * @param string $allowedChars The allowed chars to use.
     * @param callable $seeder The custom seeder for the random values to use.
     *
     * @return IString The random char list.
     *
     * @throws ArgumentOutOfRangeException $count is less than 0.
     */
    public final static function randChars(
        int $count,
        $allowedChars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
        $seeder = null) : IString
    {
        if ($count < 0) {
            throw new ArgumentOutOfRangeException($count, 'count');
        }

        $allowedChars = ClrString::valueToString($allowedChars);

        $result = '';
        foreach (static::buildRandom($count, \strlen($allowedChars), 0, $seeder) as $i) {
            $result .= $allowedChars[$i];
        }

        return new ClrString($result);
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
     * Generates a new sequence that contains one repeated value or object.
     *
     * @param $item The item to repeat.
     * @param int $count The number of items to return.
     *
     * @return IEnumerable The new sequence.
     *
     * @throws ArgumentOutOfRangeException $count is less than 0.
     */
    public final static function repeat($item, int $count) : IEnumerable {
        if ($count < 0) {
            throw new ArgumentOutOfRangeException($count, 'count');
        }

        return static::createEnumerable(static::repeatInner($item, $count));
    }

    /**
     * @see Enumerable::repeat()
     */
    protected static function repeatInner($item, int $count) {
        while ($count-- > 0) {
            yield $item;
        }
    }
}
