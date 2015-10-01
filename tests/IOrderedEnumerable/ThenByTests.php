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


function thenBySelector1FuncForTest1($x) : int {
    return strlen($x);
}

function thenBySelector2FuncForTest1($x) {
    return $x;
}

class ThenBySelector1ForTest1Class {
    public function __invoke($x) {
        return thenBySelector1FuncForTest1($x);
    }
}

class ThenBySelector2ForTest1Class {
    public function __invoke($x) {
        return thenBySelector2FuncForTest1($x);
    }
}


/**
 * @see \System\Linq\IOrderedEnumerable::thenBy()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class ThenByTests extends TestCaseBase {
    /**
     * Creates selectors for ThenByTests::test1() method.
     *
     * @return array The selectors.
     */
    protected function createSelectorsForTest1() : array {
        return [
            [
                function($x) { return thenBySelector1FuncForTest1($x); },
                function($x) { return thenBySelector2FuncForTest1($x); },
            ],
            [
                array($this, 'selector1Method1'),
                array($this, 'selector2Method1'),
            ],
            [
                array(static::class, 'selector1Method2'),
                array(static::class, 'selector2Method2'),
            ],
            [
                new ThenBySelector1ForTest1Class(),
                new ThenBySelector2ForTest1Class(),
            ],
            [
                'thenBySelector1FuncForTest1',
                'thenBySelector2FuncForTest1',
            ],
            [
                '\thenBySelector1FuncForTest1',
                '\thenBySelector2FuncForTest1',
            ],
            [
                '$x => thenBySelector1FuncForTest1($x)',
                '$x => $x',
            ],
            [
                '($x) => \thenBySelector1FuncForTest1($x)',
                '($x) => $x',
            ],
            [
                '$x => return strlen($x);',
                '$x => return thenBySelector2FuncForTest1($x);',
            ],
            [
                '($x) => return strlen($x);',
                '($x) => return \thenBySelector2FuncForTest1($x);',
            ],
            [
                '($x) => { return strlen($x); }',
                '($x) => { return $x; }',
            ],
            [
                '($x) => {
$y = strlen($x);
return $y;
}',
                '($x) => {
$y = $x;
return $y;
}',
            ],
        ];
    }

    public function test1() {
        foreach ($this->createSelectorsForTest1() as $selectors) {
            $values = [
                "grape",
                "passionfruit",
                "banana",
                "mango",
                "orange",
                "raspberry",
                "apple",
                "blueberry",
            ];

            foreach (static::sequenceListFromArray($values) as $seq) {
                /* @var IEnumerable $seq */

                $items = static::sequenceToArray($seq->orderBy($selectors[0])
                                                     ->thenBy($selectors[1]), false);

                $this->assertEquals(8, count($items));

                $this->assertSame('apple', $items[0]);
                $this->assertSame('grape', $items[1]);
                $this->assertSame('mango', $items[2]);
                $this->assertSame('banana', $items[3]);
                $this->assertSame('orange', $items[4]);
                $this->assertSame('blueberry', $items[5]);
                $this->assertSame('raspberry', $items[6]);
                $this->assertSame('passionfruit', $items[7]);
            }
        }
    }

    public function selector1Method1($x) {
        return thenBySelector1FuncForTest1($x);
    }

    public static function selector1Method2($x) {
        return thenBySelector1FuncForTest1($x);
    }

    public function selector2Method1($x) {
        return thenBySelector2FuncForTest1($x);
    }

    public static function selector2Method2($x) {
        return thenBySelector2FuncForTest1($x);
    }
}
