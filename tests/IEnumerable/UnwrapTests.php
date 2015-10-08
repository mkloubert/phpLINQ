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

use \System\Lazy;
use \System\ValueWrapper;


/**
 * @see \System\Collections\IEnumerable::unwrap()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class UnwrapTests extends TestCaseBase {
    public function test1() {
        $seq = static::sequenceFromArray([
            1,
            new ValueWrapper(2.0),
            new Lazy('() => 77'),
            null,
            new stdClass(),
            new ValueWrapper(new ValueWrapper(4)),
            new ValueWrapper(new Lazy('() => 12')),
            new Lazy('() => new \System\Lazy("() => \'hello\'")'),
            new Lazy('() => new \System\ValueWrapper(true)'),
            false,
        ]);

        $items = static::sequenceToArray($seq->unwrap());

        $this->assertEquals(10, count($items));

        $this->assertSame(1, $items[0]);
        $this->assertSame(2.0, $items[1]);
        $this->assertSame(77, $items[2]);
        $this->assertSame(null, $items[3]);
        $this->assertInstanceOf(stdClass::class, $items[4]);
        $this->assertSame(4, $items[5]);
        $this->assertSame(12, $items[6]);
        $this->assertSame('hello', $items[7]);
        $this->assertSame(true, $items[8]);
        $this->assertSame(false, $items[9]);
    }
}
