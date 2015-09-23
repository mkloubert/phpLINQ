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


function comparerFunc($x, $y) {
    return $x === $y;
}

class EqualityComparerClass {
    public function __invoke($x, $y) {
        return comparerFunc($x, $y);
    }
}

/**
 * @see \System\Collection\IEnumerable::distinct()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class DistinctTests extends TestCaseBase {
    public function comparerMethod1($x, $y) {
        return comparerFunc($x, $y);
    }

    public static function comparerMethod2($x, $y) {
        return comparerFunc($x, $y);
    }

    protected function createEqualityComparers() : array {
        return [
            function($x, $y) {
                return comparerFunc($x, $y);
            },
            'comparerFunc',
            '\comparerFunc',
            array($this, 'comparerMethod1'),
            array(static::class, 'comparerMethod2'),
            new EqualityComparerClass(),
            '$x, $y => $x === $y',
            '($x, $y) => $x === $y',
            '$x, $y => return $x === $y;',
            '($x, $y) => return $x === $y;',
            '$x, $y => { return $x === $y; }',
            '($x, $y) => { return $x === $y; }',
            '$x, $y => {
return $x === $y;
}',
            '($x, $y) => {
return $x === $y;
}',
        ];
    }

    public function testNoComparer() {
        $seq = static::sequenceFromArray([1, 2, '3', 3, 4, 5.0, 6, 5]);

        $items = static::sequenceToArray($seq->distinct());

        $this->assertEquals(6, count($items));
        $this->assertEquals(1, $items[0]);
        $this->assertEquals(2, $items[1]);
        $this->assertEquals('3', $items[2]);
        $this->assertEquals(4, $items[3]);
        $this->assertEquals(5.0, $items[4]);
        $this->assertEquals(6, $items[5]);
    }

    public function testWithComparer() {
        foreach ($this->createEqualityComparers() as $equalityComparer) {
            $seq = static::sequenceFromArray([1, 2, '3', 3, 4, 5.0, 4, 6, 5]);

            $items = static::sequenceToArray($seq->distinct($equalityComparer));

            $this->assertEquals(8, count($items));
            $this->assertEquals(1, $items[0]);
            $this->assertEquals(2, $items[1]);
            $this->assertEquals('3', $items[2]);
            $this->assertEquals(3, $items[3]);
            $this->assertEquals(4, $items[4]);
            $this->assertEquals(5.0, $items[5]);
            $this->assertEquals(6, $items[6]);
            $this->assertEquals(5, $items[7]);
        }
    }
}
