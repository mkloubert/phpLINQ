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


function selector1FuncForTest1($x) {
    return strlen($x);
}

class SelectorClass1 {
    public function __invoke($x) {
        return selector1FuncForTest1($x);
    }
}


/**
 * @see \System\Linq\IOrderedEnumerable::thenDescending()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class ThenDescendingTests extends TestCaseBase {
    /**
     * Creates selectors for ThenByTests::test1() method.
     *
     * @return array The selectors.
     */
    protected function createSelectorsForTest1() : array {
        return [
            [
                function($x) { return selector1FuncForTest1($x); },
            ],
            [
                array($this, 'selector1Method1'),
            ],
            [
                array(static::class, 'selector1Method2'),
            ],
            [
                new SelectorClass1(),
            ],
            [
                'selector1FuncForTest1',
            ],
            [
                '\selector1FuncForTest1',
            ],
            [
                '$x => strlen($x)',
            ],
            [
                '($x) => strlen($x)',
            ],
            [
                '$x => return strlen($x);',
            ],
            [
                '($x) => return strlen($x);',
            ],
            [
                '($x) => { return strlen($x); }',
            ],
            [
                '($x) => {
$y = strlen($x);
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
                                                     ->thenDescending());

                $this->assertEquals(8, count($items));

                $this->assertEquals('mango', $items[0]);
                $this->assertEquals('grape', $items[1]);
                $this->assertEquals('apple', $items[2]);
                $this->assertEquals('orange', $items[3]);
                $this->assertEquals('banana', $items[4]);
                $this->assertEquals('raspberry', $items[5]);
                $this->assertEquals('blueberry', $items[6]);
                $this->assertEquals('passionfruit', $items[7]);
            }
        }
    }

    public function selector1Method1($x) {
        return selector1FuncForTest1($x);
    }

    public static function selector1Method2($x) {
        return selector1FuncForTest1($x);
    }
}
