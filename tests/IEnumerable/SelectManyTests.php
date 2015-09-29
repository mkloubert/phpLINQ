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


function selectManySelector1Func($x) : array {
    return [$x, $x * 10, $x * 100];
}

function selectManySelector2Func($x) : Iterator {
    return new ArrayIterator(selectManySelector1Func($x));
}

function selectManySelector3Func($x) {
    foreach (selectManySelector1Func($x) as $item) {
        yield $item;
    }
}

class SelectManySelector1Class {
    public function __invoke($x) {
        return selectManySelector1Func($x);
    }
}

class SelectManySelector2Class {
    public function __invoke($x) {
        return selectManySelector2Func($x);
    }
}

class SelectManySelector3Class {
    public function __invoke($x) {
        return selectManySelector3Func($x);
    }
}

/**
 * @see \System\Collections\IEnumerable::selectMany()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class SelectManyTests extends TestCaseBase {
    /**
     * Creates the selectors for the tests.
     *
     * @return array The selectors.
     */
    protected function createSelectors() : array {
        return [
            [
                function($x) {
                    return selectManySelector1Func($x);
                },
                function($x) {
                    return selectManySelector2Func($x);
                },
                function($x) {
                    return selectManySelector3Func($x);
                },
            ],
            [
                'selectManySelector1Func',
                'selectManySelector2Func',
                'selectManySelector3Func',
            ],
            [
                '\selectManySelector1Func',
                '\selectManySelector2Func',
                '\selectManySelector3Func',
            ],
            [
                array($this, 'selector1Method1'),
                array($this, 'selector2Method1'),
                array($this, 'selector3Method1'),
            ],
            [
                array(static::class, 'selector1Method2'),
                array(static::class, 'selector2Method2'),
                array(static::class, 'selector3Method2'),
            ],
            [
                new SelectManySelector1Class(),
                new SelectManySelector2Class(),
                new SelectManySelector3Class(),
            ],
            [
                '$x => selectManySelector1Func($x)',
                '$x => selectManySelector2Func($x)',
                '$x => selectManySelector3Func($x)',
            ],
            [
                '($x) => selectManySelector1Func($x)',
                '($x) => selectManySelector2Func($x)',
                '($x) => selectManySelector3Func($x)',
            ],
            [
                '$x => return selectManySelector1Func($x);',
                '$x => return selectManySelector2Func($x);',
                '$x => return selectManySelector3Func($x);',
            ],
            [
                '($x) => return selectManySelector1Func($x);',
                '($x) => return selectManySelector2Func($x);',
                '($x) => return selectManySelector3Func($x);',
            ],
            [
                '$x => { return selectManySelector1Func($x); }',
                '$x => { return selectManySelector2Func($x); }',
                '$x => { return selectManySelector3Func($x); }',
            ],
            [
                '($x) => { return selectManySelector1Func($x); }',
                '($x) => { return selectManySelector2Func($x); }',
                '($x) => { return selectManySelector3Func($x); }',
            ],
            [
                '$x => {
return selectManySelector1Func($x);
}',
                '$x => {
return selectManySelector2Func($x);
}',
                '$x => {
return selectManySelector3Func($x);
}',
            ],
            [
                '($x) => {
return selectManySelector1Func($x);
}',
                '($x) => {
return selectManySelector2Func($x);
}',
                '($x) => {
return selectManySelector3Func($x);
}',
            ],
        ];
    }

    public function selector1Method1($x) {
        return selectManySelector1Func($x);
    }

    public static function selector1Method2($x) {
        return selectManySelector1Func($x);
    }

    public function selector2Method1($x) {
        return selectManySelector2Func($x);
    }

    public static function selector2Method2($x) {
        return selectManySelector2Func($x);
    }

    public function selector3Method1($x) {
        return selectManySelector3Func($x);
    }

    public static function selector3Method2($x) {
        return selectManySelector3Func($x);
    }

    public function test1() {
        foreach ($this->createSelectors() as $selectors) {
            foreach ($selectors as $s) {
                foreach (static::sequenceListFromArray([1, 2, 3]) as $seq) {
                    /* @var IEnumerable $seq */

                    $items = static::sequenceToArray($seq->selectMany($s));

                    $this->assertEquals(9, count($items));

                    $this->assertSame(1, $items[0]);
                    $this->assertSame(10, $items[1]);
                    $this->assertSame(100, $items[2]);
                    $this->assertSame(2, $items[3]);
                    $this->assertSame(20, $items[4]);
                    $this->assertSame(200, $items[5]);
                    $this->assertSame(3, $items[6]);
                    $this->assertSame(30, $items[7]);
                    $this->assertSame(300, $items[8]);
                }
            }
        }
    }
}
