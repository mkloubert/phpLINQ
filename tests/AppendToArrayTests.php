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


/**
 * @see \System\Collection\IEnumerable::appendToArray()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class AppendToArrayTests extends TestCaseBase {
    public function testWithKeys() {
        $seq = static::sequenceFromArray(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]);

        $items = [];
        $seq->appendToArray($items, true);

        $this->assertTrue(is_array($items));
        $this->assertEquals(5, count($items));

        $this->assertFalse(isset($items[0]));
        $this->assertTrue(isset($items['a']));
        $this->assertEquals(1, $items['a']);

        $this->assertFalse(isset($items[1]));
        $this->assertTrue(isset($items['b']));
        $this->assertEquals(2, $items['b']);

        $this->assertFalse(isset($items[2]));
        $this->assertTrue(isset($items['c']));
        $this->assertEquals(3, $items['c']);

        $this->assertFalse(isset($items[3]));
        $this->assertTrue(isset($items['d']));
        $this->assertEquals(4, $items['d']);

        $this->assertFalse(isset($items[4]));
        $this->assertTrue(isset($items['e']));
        $this->assertEquals(5, $items['e']);
    }

    public function testWithoutKeys() {
        $seq = static::sequenceFromArray(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]);

        $items = [];
        $seq->appendToArray($items);

        $this->assertTrue(is_array($items));
        $this->assertEquals(5, count($items));

        $this->assertFalse(isset($items['a']));
        $this->assertTrue(isset($items[0]));
        $this->assertEquals(1, $items[0]);

        $this->assertFalse(isset($items['b']));
        $this->assertTrue(isset($items[1]));
        $this->assertEquals(2, $items[1]);

        $this->assertFalse(isset($items['c']));
        $this->assertTrue(isset($items[2]));
        $this->assertEquals(3, $items[2]);

        $this->assertFalse(isset($items['d']));
        $this->assertTrue(isset($items[3]));
        $this->assertEquals(4, $items[3]);

        $this->assertFalse(isset($items['e']));
        $this->assertTrue(isset($items[4]));
        $this->assertEquals(5, $items[4]);
    }
}
