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


/**
 * @see \System\Collection\IEnumerable::product()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class ProductTests extends TestCaseBase {
    public function testEmpty() {
        foreach (static::sequenceListFromArray([]) as $seq) {
            /* @var IEnumerable $seq */

            $prod = $seq->product('Breaking Bad');

            $this->assertEquals('Breaking Bad', $prod);
        }
    }

    public function testFloat() {
        foreach (static::sequenceListFromArray([1.2, 3, 77, 5.67, 8]) as $seq) {
            /* @var IEnumerable $seq */

            $prod = $seq->product('Breaking Bad');

            $this->assertEquals(12573.792, $prod);
        }
    }

    public function testFloatWithZero() {
        foreach (static::sequenceListFromArray([1.2, 3, 0, 5.67, 8]) as $seq) {
            /* @var IEnumerable $seq */

            $prod = $seq->product('Breaking Bad');

            $this->assertEquals(0, $prod);
        }
    }

    public function testInt() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $prod = $seq->product('Breaking Bad');

            $this->assertEquals(120, $prod);
        }
    }

    public function testWithZero() {
        foreach (static::sequenceListFromArray([1, 2, 0, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $prod = $seq->product('Breaking Bad');

            $this->assertEquals(0, $prod);
        }
    }
}
