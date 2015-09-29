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


function distinctComparerFunc($x, $y) : bool {
    return $x === $y;
}

class DistinctEqualityComparerClass {
    public function __invoke($x, $y) {
        return distinctComparerFunc($x, $y);
    }
}

/**
 * @see \System\Collections\IEnumerable::distinct()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class DistinctTests extends TestCaseBase {
    public function comparerMethod1($x, $y) {
        return distinctComparerFunc($x, $y);
    }

    public static function comparerMethod2($x, $y) {
        return distinctComparerFunc($x, $y);
    }

    protected function createEqualityComparers() : array {
        return [
            true,
            function($x, $y) {
                return distinctComparerFunc($x, $y);
            },
            'distinctComparerFunc',
            '\distinctComparerFunc',
            array($this, 'comparerMethod1'),
            array(static::class, 'comparerMethod2'),
            new DistinctEqualityComparerClass(),
            '$x, $y => distinctComparerFunc($x, $y)',
            '($x, $y) => distinctComparerFunc($x, $y)',
            '$x, $y => return distinctComparerFunc($x, $y);',
            '($x, $y) => return distinctComparerFunc($x, $y);',
            '$x, $y => { return distinctComparerFunc($x, $y); }',
            '($x, $y) => { return distinctComparerFunc($x, $y); }',
            '$x, $y => {
return distinctComparerFunc($x, $y);
}',
            '($x, $y) => {
return distinctComparerFunc($x, $y);
}',
            '$x, $y => \distinctComparerFunc($x, $y)',
            '($x, $y) => \distinctComparerFunc($x, $y)',
            '$x, $y => return \distinctComparerFunc($x, $y);',
            '($x, $y) => return \distinctComparerFunc($x, $y);',
            '$x, $y => { return \distinctComparerFunc($x, $y); }',
            '($x, $y) => { return \distinctComparerFunc($x, $y); }',
            '$x, $y => {
return \distinctComparerFunc($x, $y);
}',
            '($x, $y) => {
return \distinctComparerFunc($x, $y);
}',
        ];
    }

    public function testNoComparer() {
        foreach (static::sequenceListFromArray([1, 2, '3', 3, 4, 5.0, 6, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $items = static::sequenceToArray($seq->distinct());

            $this->assertEquals(6, count($items));
            $this->assertSame(1, $items[0]);
            $this->assertSame(2, $items[1]);
            $this->assertSame('3', $items[2]);
            $this->assertSame(4, $items[3]);
            $this->assertSame(5.0, $items[4]);
            $this->assertSame(6, $items[5]);
        }
    }

    public function testWithComparer() {
        foreach ($this->createEqualityComparers() as $equalityComparer) {
            foreach (static::sequenceListFromArray([1, 2, '3', 3, 4, 5.0, 6, 5]) as $seq) {
                /* @var IEnumerable $seq */

                $items = static::sequenceToArray($seq->distinct($equalityComparer));

                $this->assertEquals(8, count($items));
                $this->assertSame(1, $items[0]);
                $this->assertSame(2, $items[1]);
                $this->assertSame('3', $items[2]);
                $this->assertSame(3, $items[3]);
                $this->assertSame(4, $items[4]);
                $this->assertSame(5.0, $items[5]);
                $this->assertSame(6, $items[6]);
                $this->assertSame(5, $items[7]);
            }
        }
    }
}
