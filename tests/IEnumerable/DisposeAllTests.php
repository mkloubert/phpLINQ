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

use \System\DisposableBase;
use \System\IDisposable;


class DisposeAllTestClass extends DisposableBase {
    private $_disposeValue;
    private $_value;

    public function __construct($value) {
        $this->_value = $value;
    }

    public function value() {
        return $this->_disposeValue;
    }

    protected function onDispose(bool $disposing, bool &$isDisposed) {
        if ($disposing) {
            $this->_disposeValue = $this->_value;
        }
    }
}

/**
 * @see \System\Collections\IEnumerable::disposeAll()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class DisposeAllTests extends TestCaseBase {
    public function test1() {
        $values = [
            new DisposeAllTestClass(1),
            2,
            null,
            new DisposeAllTestClass(3.4),
            '5',
        ];

        $seq = static::sequenceFromArray($values);

        $items = static::sequenceToArray($seq->disposeAll());

        $this->assertEquals(2, count($items));

        $this->assertSame(2, $items[1]);
        $this->assertSame('5', $items[4]);
    }

    public function test2() {
        $obj1 = new DisposeAllTestClass(1);
        $obj2 = new DisposeAllTestClass('blubb');

        $seq = static::sequenceFromArray([$obj1, $obj2]);

        $this->assertSame(null, $obj1->value());
        $this->assertSame(null, $obj2->value());

        $items = static::sequenceToArray($seq->disposeAll(true));

        $this->assertEquals(2, count($items));

        $this->assertSame($obj1, $items[0]);
        $this->assertInstanceOf(IDisposable::class, $items[0]);
        $this->assertTrue($items[0]->isDisposed());
        $this->assertSame(1, $items[0]->value());

        $this->assertSame($obj2, $items[1]);
        $this->assertInstanceOf(DisposeAllTestClass::class, $items[1]);
        $this->assertTrue($items[1]->isDisposed());
        $this->assertSame('blubb', $items[1]->value());
    }

    public function test3() {
        $values = [
            new DisposeAllTestClass(1),
            false,
            2,
            new DisposeAllTestClass(3.4),
            '5',
            null,
        ];

        $seq = static::sequenceFromArray($values);

        $items = static::sequenceToArray($seq->disposeAll(false, true));

        $this->assertEquals(4, count($items));

        $this->assertSame(false, $items[1]);
        $this->assertSame(2, $items[2]);
        $this->assertSame('5', $items[4]);
        $this->assertSame(null, $items[5]);
    }
}
