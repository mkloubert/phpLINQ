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


class TestClass {
    public function __toString() {
        return 'abc';
    }
}

/**
 * @see \System\Collection\IEnumerable::cast()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class CastTests extends TestCaseBase {
    public function testInt() {
        foreach (static::sequenceListFromArray([null, 1, '2', 3.0, false, 4.5, 6.30, true]) as $seq) {
            /* @var IEnumerable $seq */

            $items = static::sequenceToArray($seq->cast('int'));

            $this->assertEquals(8, count($items));

            $this->assertEquals(0, $items[0]);
            $this->assertEquals(1, $items[1]);
            $this->assertEquals(2, $items[2]);
            $this->assertEquals(3, $items[3]);
            $this->assertEquals(0, $items[4]);
            $this->assertEquals(4, $items[5]);
            $this->assertEquals(6, $items[6]);
            $this->assertEquals(1, $items[7]);

            foreach ($items as $x) {
                $this->assertTrue('integer' === gettype($x));
                $this->assertTrue(is_int($x));
                $this->assertTrue(is_integer($x));
            }
        }
    }

    public function testInteger() {
        foreach (static::sequenceListFromArray([null, 1, '2', 3.0, false, 4.5, 6.30, true, '']) as $seq) {
            /* @var IEnumerable $seq */

            $items = static::sequenceToArray($seq->cast('integer'));

            $this->assertEquals(9, count($items));

            $this->assertEquals(0, $items[0]);
            $this->assertEquals(1, $items[1]);
            $this->assertEquals(2, $items[2]);
            $this->assertEquals(3, $items[3]);
            $this->assertEquals(0, $items[4]);
            $this->assertEquals(4, $items[5]);
            $this->assertEquals(6, $items[6]);
            $this->assertEquals(1, $items[7]);
            $this->assertEquals(0, $items[8]);

            foreach ($items as $x) {
                $this->assertTrue('integer' === gettype($x));
                $this->assertTrue(is_int($x));
                $this->assertTrue(is_integer($x));
            }
        }
    }

    public function testString() {
        foreach (static::sequenceListFromArray([1, '2', 3.0, null, new TestClass(), false, 4.5, 6.70]) as $seq) {
            /* @var IEnumerable $seq */

            $items = static::sequenceToArray($seq->cast('string'));

            $this->assertEquals(8, count($items));

            $this->assertEquals('1', $items[0]);
            $this->assertEquals('2', $items[1]);
            $this->assertEquals('3', $items[2]);
            $this->assertEquals('', $items[3]);
            $this->assertEquals('abc', $items[4]);
            $this->assertEquals('', $items[5]);
            $this->assertEquals('4.5', $items[6]);
            $this->assertEquals('6.7', $items[7]);

            foreach ($items as $x) {
                $this->assertTrue('string' === gettype($x));
                $this->assertTrue(is_string($x));
            }
        }
    }
}
