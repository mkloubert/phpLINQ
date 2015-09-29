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

use \System\Collections\KeySelectorIterator;


function keySelectorIteratorKeySelectorFunc($x) {
    return strtoupper($x);
}

/**
 * @see \System\Collections\KeySelectorIterator
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class KeySelectorIteratorTests extends TestCaseBase {
    /**
     * Creates the selectors for the tests.
     *
     * @return array The selectors.
     */
    protected function createSelectors() : array {
        return [
            [
                'keySelectorIteratorKeySelectorFunc',
            ],
            [
                '\keySelectorIteratorKeySelectorFunc',
            ],
            [
                function($x) {
                    return keySelectorIteratorKeySelectorFunc($x);
                }
            ],
            [
                [$this, 'keySelectorMethod1'],
            ],
            [
                [static::class, 'keySelectorMethod2'],
            ],
            [
                '$x => keySelectorIteratorKeySelectorFunc($x)',
            ],
            [
                '($x) => keySelectorIteratorKeySelectorFunc($x)',
            ],
            [
                '$x => return keySelectorIteratorKeySelectorFunc($x);',
            ],
            [
                '($x) => return keySelectorIteratorKeySelectorFunc($x);',
            ],
            [
                '$x => { return keySelectorIteratorKeySelectorFunc($x); }',
            ],
            [
                '($x) => { return keySelectorIteratorKeySelectorFunc($x); }',
            ],
            [
                '$x => {
return keySelectorIteratorKeySelectorFunc($x);
}',
            ],
            [
                '($x) => {
return keySelectorIteratorKeySelectorFunc($x);
}',
            ],
        ];
    }

    public function keySelectorMethod1($x) {
        return static::keySelectorMethod2($x);
    }

    public static function keySelectorMethod2($x) {
        return keySelectorIteratorKeySelectorFunc($x);
    }

    public function test1() {
        foreach ($this->createSelectors() as $selectors) {
            list($keySelector) = $selectors;

            foreach (static::sequenceListFromArray(['a' => 1, 'B' => 2.0, 'c' => '3.4']) as $seq) {
                $destIterator = new KeySelectorIterator($seq,
                                                        $keySelector);

                $arr = iterator_to_array($destIterator);

                $this->assertEquals(3, count($arr));

                $this->assertFalse(isset($arr['a']));
                $this->assertFalse(isset($arr[0]));
                $this->assertTrue(isset($arr['A']));
                $this->assertSame(1, $arr['A']);

                $this->assertFalse(isset($arr['b']));
                $this->assertFalse(isset($arr[1]));
                $this->assertTrue(isset($arr['B']));
                $this->assertSame(2.0, $arr['B']);

                $this->assertFalse(isset($arr['c']));
                $this->assertFalse(isset($arr[2]));
                $this->assertTrue(isset($arr['C']));
                $this->assertSame('3.4', $arr['C']);
            }
        }
    }
}
