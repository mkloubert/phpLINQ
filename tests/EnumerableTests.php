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

use \System\Linq\Enumerable;


function enumerableRangeIncreaseByFunc() {
    return 0.5;
}

class EnumerableRangeIncreaseByFuncClass {
    public function __invoke() {
        return enumerableRangeIncreaseByFunc();
    }
}

/**
 * Tests for \System\Linq\Enumerable class.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class EnumerableTests extends TestCaseBase {
    /**
     * Creates the $increaseBy callable values for the Enumerable:range() tests.
     *
     * @return array The functions.
     */
    protected function createRangeIncreaseByFuncs() : array {
        return [
            function() {
                return enumerableRangeIncreaseByFunc();
            },
            'enumerableRangeIncreaseByFunc',
            '\enumerableRangeIncreaseByFunc',
            [$this, 'rangeIncreaseByFuncMethod1'],
            [static::class, 'rangeIncreaseByFuncMethod2'],
            new EnumerableRangeIncreaseByFuncClass(),
            '() => enumerableRangeIncreaseByFunc()',
            '() => \enumerableRangeIncreaseByFunc()',
            '() => return enumerableRangeIncreaseByFunc();',
            '() => return \enumerableRangeIncreaseByFunc();',
            '() => { return enumerableRangeIncreaseByFunc(); }',
            '() => { return \enumerableRangeIncreaseByFunc(); }',
            '() => {
    return enumerableRangeIncreaseByFunc();
}',
            '() => {
    return \enumerableRangeIncreaseByFunc();
}',
        ];
    }

    public function rangeIncreaseByFuncMethod1() {
        return static::rangeIncreaseByFuncMethod2();
    }

    public static function rangeIncreaseByFuncMethod2() {
        return enumerableRangeIncreaseByFunc();
    }

    public function testBuildRandom() {
        $seq = Enumerable::buildRandom(5);

        $items = static::sequenceToArray($seq);

        $this->assertEquals(5, count($items));
    }

    public function testRange1() {
        $seq = Enumerable::range(1, 5);

        $items = static::sequenceToArray($seq);

        $this->assertEquals(5, count($items));
        foreach ($items as $key => $value) {
            $this->assertSame($key + 1, $value);
        }
    }

    public function testRange2() {
        $seq = Enumerable::range(1, 0);

        $items = static::sequenceToArray($seq);

        $this->assertEquals(0, count($items));
    }

    public function testRange3() {
        foreach ($this->createRangeIncreaseByFuncs() as $increaseByFunc) {
            $seq = Enumerable::range(2, 3, $increaseByFunc);

            $items = static::sequenceToArray($seq);

            $this->assertEquals(3, count($items));

            $this->assertTrue(isset($items[0]));
            $this->assertSame(2, $items[0]);

            $this->assertTrue(isset($items[1]));
            $this->assertSame(2.5, $items[1]);

            $this->assertTrue(isset($items[2]));
            $this->assertSame(3.0, $items[2]);
        }
    }
}

