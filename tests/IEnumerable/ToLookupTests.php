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


function toLookupElementSelectorForTest1Func(Package $x) : string {
    return sprintf('%s %s',
                   $x->Company, $x->TrackingNumber);
}

function toLookupKeySelectorForTest1Func(Package $x) : string {
    return strtoupper($x->Company[0]);
}

class ToLookupElementSelectorForTest1Class {
    public function __invoke($x) {
        return toLookupElementSelectorForTest1Func($x);
    }
}

class ToLookupKeySelectorForTest1Class {
    public function __invoke($x) {
        return toLookupKeySelectorForTest1Func($x);
    }
}

/**
 * @see \System\Collections\IEnumerable::toList()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class ToLookupTests extends TestCaseBase {
    /**
     * Creates the element selectors for the tests.
     *
     * @return array The element selectors.
     */
    protected function createElementSelectors() : array {
        return [
            function($x) {
                return toLookupElementSelectorForTest1Func($x);
            },
            'toLookupElementSelectorForTest1Func',
            '\toLookupElementSelectorForTest1Func',
            [$this, 'elementSelectorForTest1Method1'],
            [static::class, 'elementSelectorForTest1Method2'],
            new ToLookupElementSelectorForTest1Class(),
            '$x => toLookupElementSelectorForTest1Func($x)',
            '$x => \toLookupElementSelectorForTest1Func($x)',
            '($x) => toLookupElementSelectorForTest1Func($x)',
            '($x) => \toLookupElementSelectorForTest1Func($x)',
            '$x => return toLookupElementSelectorForTest1Func($x);',
            '$x => return \toLookupElementSelectorForTest1Func($x);',
            '($x) => return toLookupElementSelectorForTest1Func($x);',
            '($x) => return \toLookupElementSelectorForTest1Func($x);',
            '$x => { return toLookupElementSelectorForTest1Func($x); }',
            '$x => { return \toLookupElementSelectorForTest1Func($x); }',
            '($x) => { return toLookupElementSelectorForTest1Func($x); }',
            '($x) => { return \toLookupElementSelectorForTest1Func($x); }',
            '$x => {
                return toLookupElementSelectorForTest1Func($x);
            }',
            '$x => {
                return \toLookupElementSelectorForTest1Func($x);
            }',
            '($x) => {
                return toLookupElementSelectorForTest1Func($x);
            }',
            '($x) => {
                return \toLookupElementSelectorForTest1Func($x);
            }',
        ];
    }

    /**
     * Creates the key selectors for the tests.
     *
     * @return array The element selectors.
     */
    protected function createKeySelectors() : array {
        return [
            function($x) {
                return toLookupKeySelectorForTest1Func($x);
            },
            'toLookupKeySelectorForTest1Func',
            '\toLookupKeySelectorForTest1Func',
            [$this, 'keySelectorForTest1Method1'],
            [static::class, 'keySelectorForTest1Method2'],
            new ToLookupKeySelectorForTest1Class(),
            '$x => toLookupKeySelectorForTest1Func($x)',
            '$x => \toLookupKeySelectorForTest1Func($x)',
            '($x) => toLookupKeySelectorForTest1Func($x)',
            '($x) => \toLookupKeySelectorForTest1Func($x)',
            '$x => return toLookupKeySelectorForTest1Func($x);',
            '$x => return \toLookupKeySelectorForTest1Func($x);',
            '($x) => return toLookupKeySelectorForTest1Func($x);',
            '($x) => return \toLookupKeySelectorForTest1Func($x);',
            '$x => { return toLookupKeySelectorForTest1Func($x); }',
            '$x => { return \toLookupKeySelectorForTest1Func($x); }',
            '($x) => { return toLookupKeySelectorForTest1Func($x); }',
            '($x) => { return \toLookupKeySelectorForTest1Func($x); }',
            '$x => {
                return toLookupKeySelectorForTest1Func($x);
            }',
            '$x => {
                return \toLookupKeySelectorForTest1Func($x);
            }',
            '($x) => {
                return toLookupKeySelectorForTest1Func($x);
            }',
            '($x) => {
                return \toLookupKeySelectorForTest1Func($x);
            }',
        ];
    }

    public function elementSelectorForTest1Method1($x) {
        return static::elementSelectorForTest1Method2($x);
    }

    public static function elementSelectorForTest1Method2($x) {
        return toLookupElementSelectorForTest1Func($x);
    }

    public function keySelectorForTest1Method1($x) {
        return static::keySelectorForTest1Method2($x);
    }

    public static function keySelectorForTest1Method2($x) {
        return toLookupKeySelectorForTest1Func($x);
    }

    public function test1() {
        $data = [
            new Package('Coho Vineyard', 25.2, 1),
            new Package('Lucerne Publishing', 18.7, 2),
            new Package('Wingtip Toys', 6, 3),
            new Package('Contoso Pharmaceuticals', 9.3, 4),
            new Package('Wide World Importers', 33.8, 5),
        ];

        foreach ($this->createKeySelectors() as $keySelector) {
            foreach ($this->createElementSelectors() as $elementSelector) {
                foreach (static::sequenceListFromArray($data) as $seq) {
                    /* @var IEnumerable $seq */

                    $lu = $seq->toLookup($keySelector, null, $elementSelector);

                    $this->assertEquals(3, count($lu));

                    $this->assertTrue(isset($lu['C']));
                    $this->assertInstanceOf(IEnumerable::class, $lu['C']);
                    $this->checkForExpectedValues($lu['C']->asResettable(),
                                                  ['Coho Vineyard 1', 'Contoso Pharmaceuticals 4']);

                    $this->assertTrue(isset($lu['L']));
                    $this->assertInstanceOf(IEnumerable::class, $lu['L']);
                    $this->checkForExpectedValues($lu['L']->asResettable(),
                                                  ['Lucerne Publishing 2']);

                    $this->assertTrue(isset($lu['W']));
                    $this->assertInstanceOf(IEnumerable::class, $lu['W']);
                    $this->checkForExpectedValues($lu['W']->asResettable(),
                                                  ['Wingtip Toys 3', 'Wide World Importers 5']);
                }
            }
        }
    }
}
