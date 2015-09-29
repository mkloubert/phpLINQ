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

use \System\Collections\IEnumerable;


function exceptEqualityComparerFunc($x, $y) : bool {
    return $x === $y;
}

class ExceptEqualityComparerClass {
    public function __invoke($x, $y) {
        return exceptEqualityComparerFunc($x, $y);
    }
}

/**
 * @see \System\Collection\IEnumerable::except()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class ExceptTests extends TestCaseBase {
    /**
     * Creates the equality comparers for the tests.
     *
     * @return array The equality comparers.
     */
    protected function createEqualityComparers() : array {
        return [
            true,
            function ($x, $y) : bool {
                return exceptEqualityComparerFunc($x, $y);
            },
            'exceptEqualityComparerFunc',
            '\exceptEqualityComparerFunc',
            array($this, 'equalityComparerMethod1'),
            array(static::class, 'equalityComparerMethod2'),
            new ExceptEqualityComparerClass(),
            '$x, $y => exceptEqualityComparerFunc($x, $y)',
            '($x, $y) => exceptEqualityComparerFunc($x, $y)',
            '$x, $y => return exceptEqualityComparerFunc($x, $y);',
            '($x, $y) => return exceptEqualityComparerFunc($x, $y);',
            '$x, $y => { return exceptEqualityComparerFunc($x, $y); }',
            '($x, $y) => { return exceptEqualityComparerFunc($x, $y); }',
            '$x, $y => {
return exceptEqualityComparerFunc($x, $y);
}',
            '($x, $y) => {
return exceptEqualityComparerFunc($x, $y);
}',
            '$x, $y => \exceptEqualityComparerFunc($x, $y)',
            '($x, $y) => \exceptEqualityComparerFunc($x, $y)',
            '$x, $y => return \exceptEqualityComparerFunc($x, $y);',
            '($x, $y) => return \exceptEqualityComparerFunc($x, $y);',
            '$x, $y => { return \exceptEqualityComparerFunc($x, $y); }',
            '($x, $y) => { return \exceptEqualityComparerFunc($x, $y); }',
            '$x, $y => {
return \exceptEqualityComparerFunc($x, $y);
}',
            '($x, $y) => {
return \exceptEqualityComparerFunc($x, $y);
}',
        ];
    }

    public function equalityComparerMethod1($x, $y) : bool {
        return exceptEqualityComparerFunc($x, $y);
    }

    public static function equalityComparerMethod2($x, $y) : bool {
        return exceptEqualityComparerFunc($x, $y);
    }

    public function test1Array() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $ex = [2, 5];

            $items = static::sequenceToArray($seq->except($ex));

            $this->assertEquals(3, count($items));
            $this->assertTrue(1 === $items[0]);
            $this->assertTrue(3 === $items[1]);
            $this->assertTrue(4 === $items[2]);
        }
    }

    public function test1Generator() {
        $createGenerator = function() {
            yield 3;
            yield 2;
            yield 4;
        };

        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $ex = $createGenerator();

            $items = static::sequenceToArray($seq->except($ex));

            $this->assertEquals(2, count($items));
            $this->assertTrue(1 === $items[0]);
            $this->assertTrue(5 === $items[1]);
        }
    }

    public function test1Iterator() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $ex = new ArrayIterator([2, 4]);

            $items = static::sequenceToArray($seq->except($ex));

            $this->assertEquals(3, count($items));
            $this->assertTrue(1 === $items[0]);
            $this->assertTrue(3 === $items[1]);
            $this->assertTrue(5 === $items[2]);
        }
    }

    public function test2Array() {
        foreach ($this->createEqualityComparers() as $equalityComparer) {
            foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
                /* @var IEnumerable $seq */

                $ex = [2, '5'];

                $items = static::sequenceToArray($seq->except($ex, $equalityComparer));

                $this->assertEquals(4, count($items));
                $this->assertTrue(1 === $items[0]);
                $this->assertTrue(3 === $items[1]);
                $this->assertTrue(4 === $items[2]);
                $this->assertTrue(5 === $items[3]);
            }
        }
    }

    public function test2Generator() {
        $createGenerator = function() {
            yield 3;
            yield 2.0;
            yield 4;
        };

        foreach ($this->createEqualityComparers() as $equalityComparer) {
            foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
                /* @var IEnumerable $seq */

                $ex = $createGenerator();

                $items = static::sequenceToArray($seq->except($ex, $equalityComparer));

                $this->assertEquals(3, count($items));
                $this->assertTrue(1 === $items[0]);
                $this->assertTrue(2 === $items[1]);
                $this->assertTrue(5 === $items[2]);
            }
        }
    }

    public function test2Iterator() {
        foreach ($this->createEqualityComparers() as $equalityComparer) {
            foreach (static::sequenceListFromArray(['1', 2, 3, 4, '5']) as $seq) {
                /* @var IEnumerable $seq */

                $ex = new ArrayIterator([3, 4, 1]);

                $items = static::sequenceToArray($seq->except($ex, $equalityComparer));

                $this->assertEquals(3, count($items));
                $this->assertTrue('1' === $items[0]);
                $this->assertTrue(2 === $items[1]);
                $this->assertTrue('5' === $items[2]);
            }
        }
    }
}
