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

use \System\ArgumentException;
use \System\Collections\Dictionary;


function keyComparer($x, $y) : bool {
    return 0 === strcasecmp(trim($x), trim($y));
}

function keyValidator($x) : bool {
    return is_string($x);
}

function valueValidator($x) : bool {
    return is_numeric($x);
}

/**
 * Tests for \System\Collections\Dictionary class.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class DictionaryTests extends TestCaseBase {
    /**
     * Creates the key equality comparers for the tests.
     *
     * @return array The equality comparers for the tests.
     */
    protected function createKeyComparers() : array {
        return [
            function($x, $y) {
                return keyComparer($x, $y);
            },
            'keyComparer',
            '\keyComparer',
        ];
    }

    /**
     * Creates the key validators for the tests.
     *
     * @return array The key validators.
     */
    protected function createKeyValidators() : array {
        return [
            function($x) {
                return keyValidator($x);
            },
            'keyValidator',
            '\keyValidator',
        ];
    }

    /**
     * Creates the value validators for the tests.
     *
     * @return array The value validators.
     */
    protected function createValueValidators() : array {
        return [
            function($x) {
                return valueValidator($x);
            },
            'valueValidator',
            '\valueValidator',
        ];
    }

    public function testAdd() {
        $dict = new Dictionary();

        $this->assertEquals(0, count($dict));

        $dict->add('a', 1.2);
        $this->assertEquals(1, count($dict));
        $this->assertEquals(1, count($dict->keys()));
        $this->checkForExpectedValues($dict->keys()->asResettable(), ['a']);
        $this->assertEquals(1, count($dict->values()));
        $this->checkForExpectedValues($dict->values()->asResettable(), [1.2]);

        $dict->add('b', 7);
        $this->assertEquals(2, count($dict));
        $this->assertEquals(2, count($dict->keys()));
        $this->checkForExpectedValues($dict->keys()->asResettable(), ['a', 'b']);
        $this->assertEquals(2, count($dict->values()));
        $this->checkForExpectedValues($dict->values()->asResettable(), [1.2, 7]);

        $dict->add('0', 'nadndsakw245');
        $this->assertEquals(3, count($dict));
        $this->assertEquals(3, count($dict->keys()));
        $this->checkForExpectedValues($dict->keys()->asResettable(), ['a', 'b', '0']);
        $this->assertEquals(3, count($dict->values()));
        $this->checkForExpectedValues($dict->values()->asResettable(), [1.2, 7, 'nadndsakw245']);
    }

    public function testAddWithKeyComparer() {
        foreach ($this->createKeyComparers() as $keyEqualityComparer) {
            $dict = new Dictionary(null, $keyEqualityComparer);

            $this->assertEquals(0, count($dict));

            $dict->add('A', 1.2);
            $this->assertEquals(1, count($dict));
            $this->assertEquals(1, count($dict->keys()));
            $this->checkForExpectedValues($dict->keys()->asResettable(), ['A']);
            $this->assertEquals(1, count($dict->values()));
            $this->checkForExpectedValues($dict->values()->asResettable(), [1.2]);

            foreach (['a', 'A', 'a ', ' A ', ' A   '] as $key) {
                unset($thrownEx);

                $this->assertTrue(isset($dict[$key]));

                try {
                    $dict->add($key, 12);
                }
                catch (ArgumentException $ex) {
                    $thrownEx = $ex;
                }

                $this->assertTrue(isset($thrownEx));
                $this->assertInstanceOf(ArgumentException::class, $thrownEx);

                $this->assertEquals(1, count($dict));
                $this->assertEquals(1, count($dict->keys()));
                $this->checkForExpectedValues($dict->keys()->asResettable(), ['A']);
                $this->assertEquals(1, count($dict->values()));
                $this->checkForExpectedValues($dict->values()->asResettable(), [1.2]);
            }

            $dict->add(' b', 888);
            $this->assertEquals(2, count($dict));
            $this->assertTrue(isset($dict[' b']));
            $this->assertTrue(isset($dict['B']));
            $this->assertTrue(isset($dict['b']));
            $this->assertTrue(isset($dict['b ']));
            $this->assertTrue(isset($dict['  B']));
            $this->assertTrue(isset($dict[' b  ']));
            $this->assertTrue(isset($dict['  B   ']));
            $this->assertEquals(2, count($dict->keys()));
            $this->checkForExpectedValues($dict->keys()->asResettable(), ['A', ' b']);
            $this->assertEquals(2, count($dict->values()));
            $this->checkForExpectedValues($dict->values()->asResettable(), [1.2, 888]);
        }
    }

    public function testAddWithKeyValidator() {
        foreach ($this->createKeyValidators() as $keyValidator) {
            $dict = new Dictionary(null, null, $keyValidator);

            $this->assertEquals(0, count($dict));

            $dict->add('A', 1.2);
            $this->assertEquals(1, count($dict));
            $this->assertTrue(isset($dict['A']));
            $this->assertEquals(1, count($dict->keys()));
            $this->checkForExpectedValues($dict->keys()->asResettable(), ['A']);
            $this->assertEquals(1, count($dict->values()));
            $this->checkForExpectedValues($dict->values()->asResettable(), [1.2]);

            foreach ([ord('A'), 2, 4.5, false, null, new stdClass()] as $key) {
                unset($thrownEx);

                try {
                    $dict->add($key, 23979);
                }
                catch (ArgumentException $ex) {
                    $thrownEx = $ex;
                }

                $this->assertTrue(isset($thrownEx));
                $this->assertInstanceOf(ArgumentException::class, $thrownEx);
            }

            $dict->add('b', 3);
            $this->assertEquals(2, count($dict));
            $this->assertTrue(isset($dict['b']));
            $this->assertEquals(2, count($dict->keys()));
            $this->checkForExpectedValues($dict->keys()->asResettable(), ['A', 'b']);
            $this->assertEquals(2, count($dict->values()));
            $this->checkForExpectedValues($dict->values()->asResettable(), [1.2, 3]);
        }
    }

    public function testAddWithValueValidator() {
        foreach ($this->createValueValidators() as $valueValidator) {
            $dict = new Dictionary(null, null, null, $valueValidator);

            $this->assertEquals(0, count($dict));

            $dict->add('A', 1.2);
            $this->assertEquals(1, count($dict));
            $this->assertTrue(isset($dict['A']));
            $this->assertEquals(1, count($dict->keys()));
            $this->checkForExpectedValues($dict->keys()->asResettable(), ['A']);
            $this->assertEquals(1, count($dict->values()));
            $this->checkForExpectedValues($dict->values()->asResettable(), [1.2]);

            foreach (['xyz', false, null, new stdClass()] as $value) {
                unset($thrownEx);

                try {
                    $dict->add('b', $value);
                }
                catch (ArgumentException $ex) {
                    $thrownEx = $ex;
                }

                $this->assertTrue(isset($thrownEx));
                $this->assertInstanceOf(ArgumentException::class, $thrownEx);
            }

            $dict->add('b', 3);
            $this->assertEquals(2, count($dict));
            $this->assertTrue(isset($dict['b']));
            $this->assertEquals(2, count($dict->keys()));
            $this->checkForExpectedValues($dict->keys()->asResettable(), ['A', 'b']);
            $this->assertEquals(2, count($dict->values()));
            $this->checkForExpectedValues($dict->values()->asResettable(), [1.2, 3]);

            $dict->add('c', '4.5');
            $this->assertEquals(3, count($dict));
            $this->assertTrue(isset($dict['c']));
            $this->assertEquals(3, count($dict->keys()));
            $this->checkForExpectedValues($dict->keys()->asResettable(), ['A', 'b', 'c']);
            $this->assertEquals(3, count($dict->values()));
            $this->checkForExpectedValues($dict->values()->asResettable(), [1.2, 3, '4.5']);
        }
    }
}
