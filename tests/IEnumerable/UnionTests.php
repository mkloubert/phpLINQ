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


function unionEqualityComparerFunc($x, $y) : bool {
    return $x === $y;
}

class UnionEqualityComparerClass {
    public function __invoke($x, $y) : bool {
        return unionEqualityComparerFunc($x, $y);
    }
}

/**
 * @see \System\Collections\IEnumerable::union()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class UnionTests extends TestCaseBase {
    /**
     * Creates the equality comparers for the tests.
     *
     * @return array The equality comparers.
     */
    protected function createEqualityComparers() : array {
        return [
            true,
            function($x, $y) : bool {
                return unionEqualityComparerFunc($x, $y);
            },
            'unionEqualityComparerFunc',
            '\unionEqualityComparerFunc',
            array($this, 'equalityComparerMethod1'),
            array(static::class, 'equalityComparerMethod2'),
            new UnionEqualityComparerClass(),
            '$x, $y => unionEqualityComparerFunc($x, $y)',
            '($x, $y) => unionEqualityComparerFunc($x, $y)',
            '$x, $y => return unionEqualityComparerFunc($x, $y);',
            '($x, $y) => return unionEqualityComparerFunc($x, $y);',
            '$x, $y => { return unionEqualityComparerFunc($x, $y); }',
            '($x, $y) => { return unionEqualityComparerFunc($x, $y); }',
            '$x, $y => {
return unionEqualityComparerFunc($x, $y);
}',
            '($x, $y) => {
return unionEqualityComparerFunc($x, $y);
}',
            '$x, $y => \unionEqualityComparerFunc($x, $y)',
            '($x, $y) => \unionEqualityComparerFunc($x, $y)',
            '$x, $y => return \unionEqualityComparerFunc($x, $y);',
            '($x, $y) => return \unionEqualityComparerFunc($x, $y);',
            '$x, $y => { return \unionEqualityComparerFunc($x, $y); }',
            '($x, $y) => { return \unionEqualityComparerFunc($x, $y); }',
            '$x, $y => {
return \unionEqualityComparerFunc($x, $y);
}',
            '($x, $y) => {
return \unionEqualityComparerFunc($x, $y);
}',
        ];
    }

    public function equalityComparerMethod1($x, $y) : bool {
        return unionEqualityComparerFunc($x, $y);
    }

    public static function equalityComparerMethod2($x, $y) : bool {
        return unionEqualityComparerFunc($x, $y);
    }

    public function test1Array() {
        foreach (static::sequenceListFromArray([5, 3, 9, 7, 5, 9, 3, 7]) as $seq) {
            /* @var IEnumerable $seq */

            $other = [8, 3, 6, 4, 4, 9, 1, 0];

            $items = static::sequenceToArray($seq->union($other), false);

            $this->assertEquals(9, count($items));

            $this->assertSame(5, $items[0]);
            $this->assertSame(3, $items[1]);
            $this->assertSame(9, $items[2]);
            $this->assertSame(7, $items[3]);
            $this->assertSame(8, $items[4]);
            $this->assertSame(6, $items[5]);
            $this->assertSame(4, $items[6]);
            $this->assertSame(1, $items[7]);
            $this->assertSame(0, $items[8]);
        }
    }

    public function test1Generator() {
        $createGenerator = function() {
            yield 8;
            yield 3;
            yield 6;
            yield 4;
            yield 4;
            yield 9;
            yield 1;
            yield 0;
        };

        foreach (static::sequenceListFromArray([5, 3, 9, 7, 5, 9, 3, 7]) as $seq) {
            /* @var IEnumerable $seq */

            $other = $createGenerator();

            $items = static::sequenceToArray($seq->union($other), false);

            $this->assertEquals(9, count($items));

            $this->assertSame(5, $items[0]);
            $this->assertSame(3, $items[1]);
            $this->assertSame(9, $items[2]);
            $this->assertSame(7, $items[3]);
            $this->assertSame(8, $items[4]);
            $this->assertSame(6, $items[5]);
            $this->assertSame(4, $items[6]);
            $this->assertSame(1, $items[7]);
            $this->assertSame(0, $items[8]);
        }
    }

    public function test1Iterator() {
        foreach (static::sequenceListFromArray([5, 3, 9, 7, 5, 9, 3, 7]) as $seq) {
            /* @var IEnumerable $seq */

            $other = new ArrayIterator([8, 3, 6, 4, 4, 9, 1, 0]);

            $items = static::sequenceToArray($seq->union($other), false);

            $this->assertEquals(9, count($items));

            $this->assertSame(5, $items[0]);
            $this->assertSame(3, $items[1]);
            $this->assertSame(9, $items[2]);
            $this->assertSame(7, $items[3]);
            $this->assertSame(8, $items[4]);
            $this->assertSame(6, $items[5]);
            $this->assertSame(4, $items[6]);
            $this->assertSame(1, $items[7]);
            $this->assertSame(0, $items[8]);
        }
    }

    public function test2Array() {
        foreach ($this->createEqualityComparers() as $equalityComparer) {
            foreach (static::sequenceListFromArray([5, 3, 9, 7, 5, 9, 3, 7]) as $seq) {
                /* @var IEnumerable $seq */

                $other = [8, 3, 6, 4, '4', 9, 1, 0];

                $items = static::sequenceToArray($seq->union($other, $equalityComparer), false);

                $this->assertEquals(10, count($items));

                $this->assertSame(5, $items[0]);
                $this->assertSame(3, $items[1]);
                $this->assertSame(9, $items[2]);
                $this->assertSame(7, $items[3]);
                $this->assertSame(8, $items[4]);
                $this->assertSame(6, $items[5]);
                $this->assertSame(4, $items[6]);
                $this->assertSame('4', $items[7]);
                $this->assertSame(1, $items[8]);
                $this->assertSame(0, $items[9]);
            }
        }
    }

    public function test2Generator() {
        $createGenerator = function() {
            yield 8;
            yield 3;
            yield 6;
            yield 4;
            yield 4.0;
            yield 9;
            yield 1;
            yield 0;
        };

        foreach ($this->createEqualityComparers() as $equalityComparer) {
            foreach (static::sequenceListFromArray([5, 3, 9, 7, 5, 9, 3, 7]) as $seq) {
                /* @var IEnumerable $seq */

                $other = $createGenerator();

                $items = static::sequenceToArray($seq->union($other, $equalityComparer), false);

                $this->assertEquals(10, count($items));

                $this->assertSame(5, $items[0]);
                $this->assertSame(3, $items[1]);
                $this->assertSame(9, $items[2]);
                $this->assertSame(7, $items[3]);
                $this->assertSame(8, $items[4]);
                $this->assertSame(6, $items[5]);
                $this->assertSame(4, $items[6]);
                $this->assertSame(4.0, $items[7]);
                $this->assertSame(1, $items[8]);
                $this->assertSame(0, $items[9]);
            }
        }
    }

    public function test2Iterator() {
        foreach ($this->createEqualityComparers() as $equalityComparer) {
            foreach (static::sequenceListFromArray([5, 3, 9, 7, 5, 9, 3, 7]) as $seq) {
                /* @var IEnumerable $seq */

                $other = new ArrayIterator([8, 3, 6, 4, '4', 9, 1, 0]);

                $items = static::sequenceToArray($seq->union($other, $equalityComparer), false);

                $this->assertEquals(10, count($items));

                $this->assertSame(5, $items[0]);
                $this->assertSame(3, $items[1]);
                $this->assertSame(9, $items[2]);
                $this->assertSame(7, $items[3]);
                $this->assertSame(8, $items[4]);
                $this->assertSame(6, $items[5]);
                $this->assertSame(4, $items[6]);
                $this->assertSame('4', $items[7]);
                $this->assertSame(1, $items[8]);
                $this->assertSame(0, $items[9]);
            }
        }
    }
}
