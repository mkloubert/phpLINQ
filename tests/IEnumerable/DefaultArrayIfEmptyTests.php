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
 * @see \System\Collection\IEnumerable::defaultArrayIfEmpty()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class DefaultArrayIfEmptyTests extends TestCaseBase {
    public function test1() {
        foreach (static::sequenceListFromArray([1, 2, 3]) as $seq) {
            /* @var IEnumerable $seq */

            $items = static::sequenceToArray($seq->defaultArrayIfEmpty(['A', 'B']));

            $this->assertEquals(3, count($items));
            foreach ($items as $key => $value) {
                $this->assertTrue('integer' === gettype($value));
                $this->assertTrue(is_int($value));
                $this->assertTrue(is_integer($value));
                $this->assertEquals($key + 1, $value);
            }
        }
    }

    public function test2() {
        foreach (static::sequenceListFromArray([]) as $seq) {
            /* @var IEnumerable $seq */

            $items = static::sequenceToArray($seq->defaultArrayIfEmpty(['A', 'B']));

            $this->assertEquals(2, count($items));
            foreach ($items as $key => $value) {
                $this->assertTrue('string' === gettype($value));
                $this->assertTrue(is_string($value));
                $this->assertEquals(chr(ord('A') + $key), $value);
            }
        }
    }
}