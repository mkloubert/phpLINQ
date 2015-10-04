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

use \System\Object;
use \System\IValueWrapper;
use \System\ValueWrapper;


function converterSumFunc($a, $b) {
    return $a + $b;
}

class converterSumClass {
    public function __invoke($a, $b) {
        return converterSumFunc($a, $b);
    }
}

/**
 * Tests for converting objects and values.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class ConverterTests extends TestCaseBase {
    protected function createSumFunctions() : array {
        return [
            'converterSumFunc',
            '\converterSumFunc',
            function($a, $b) {
                return converterSumFunc($a, $b);
            },
            [$this, 'sumMethod1'],
            [static::class, 'sumMethod2'],
            new converterSumClass(),
            '$a, $b => converterSumFunc($a, $b)',
            '$a, $b => \converterSumFunc($a, $b)',
            '($a, $b) => converterSumFunc($a, $b)',
            '($a, $b) => \converterSumFunc($a, $b)',
            '$a, $b => return converterSumFunc($a, $b);',
            '$a, $b => return \converterSumFunc($a, $b);',
            '($a, $b) => return converterSumFunc($a, $b);',
            '($a, $b) => return \converterSumFunc($a, $b);',
            '$a, $b => { return converterSumFunc($a, $b); }',
            '$a, $b => { return \converterSumFunc($a, $b); }',
            '($a, $b) => { return converterSumFunc($a, $b); }',
            '($a, $b) => { return \converterSumFunc($a, $b); }',
            '$a, $b => {
return converterSumFunc($a, $b);
}',
            '$a, $b => {
return \converterSumFunc($a, $b);
}',
            '($a, $b) => {
return converterSumFunc($a, $b);
}',
            '($a, $b) => {
return \converterSumFunc($a, $b);
}',
        ];
    }

    public function sumMethod1($a, $b) {
        return static::sumMethod2($a, $b);
    }

    public static function sumMethod2($a, $b) {
        return converterSumFunc($a, $b);
    }

    public function testCallable() {
        foreach ($this->createSumFunctions() as $sumFunc) {
            foreach (['callable', 'function'] as $targetType) {
                /* @var callable $func1 */
                /* @var callable $func2 */
                /* @var callable $func3 */
                /* @var callable $func4 */
                /* @var callable $func5 */

                $obj1 = new ValueWrapper('1');
                $obj2 = new ValueWrapper(2);
                $obj3 = new ValueWrapper(3.0);
                $obj4 = new ValueWrapper(4.5);
                $obj5 = new ValueWrapper(Object::asCallable($sumFunc));

                $this->assertSame('1', $obj1->getWrappedValue());
                $this->assertSame(2, $obj2->getWrappedValue());
                $this->assertSame(3.0, $obj3->getWrappedValue());
                $this->assertSame(4.5, $obj4->getWrappedValue());
                $this->assertTrue(is_callable($obj5->getWrappedValue()));

                $func1 = $obj1->toType($targetType);
                $func2 = $obj2->toType($targetType);
                $func3 = $obj3->toType($targetType);
                $func4 = $obj4->toType($targetType);
                $func5 = $obj5->toType($targetType);

                $this->assertTrue(is_callable($func1));
                $this->assertTrue(is_callable($func2));
                $this->assertTrue(is_callable($func3));
                $this->assertTrue(is_callable($func4));
                $this->assertTrue(is_callable($func5));

                $this->assertSame($func1(), $obj1->getWrappedValue());
                $this->assertSame($func2(), $obj2->getWrappedValue());
                $this->assertSame($func3(), $obj3->getWrappedValue());
                $this->assertSame($func4(), $obj4->getWrappedValue());
                $this->assertSame($func5, $obj5->getWrappedValue());

                $this->assertSame(6.5, $func5(2, 4.5));
            }
        }
    }

    public function testValueWrapper() {
        $obj1 = new ValueWrapper('1');
        $obj2 = new ValueWrapper(2);
        $obj3 = new ValueWrapper(3.0);
        $obj4 = new ValueWrapper(4.5);

        $this->assertSame('1', $obj1->getWrappedValue());
        $this->assertSame('1', $obj1->toType('string'));
        $this->assertSame(null, $obj1->toType('unset'));
        $this->assertSame(null, $obj1->toType('null'));
        $this->assertSame(1, $obj1->toType('int'));
        $this->assertSame(1.0, $obj1->toType('float'));

        $this->assertSame(2, $obj2->getWrappedValue());
        $this->assertSame('2', $obj2->toType('string'));
        $this->assertSame(2.0, $obj2->toType('float'));
        $this->assertSame(null, $obj2->toType('unset'));
        $this->assertSame(null, $obj2->toType('null'));

        $this->assertSame(3.0, $obj3->getWrappedValue());
        $this->assertSame('3', $obj3->toType('string'));
        $this->assertSame(3, $obj3->toType('int'));
        $this->assertSame(null, $obj3->toType('unset'));
        $this->assertSame(null, $obj3->toType('null'));

        $this->assertSame(4.5, $obj4->getWrappedValue());
        $this->assertSame('4.5', $obj4->toType('string'));
        $this->assertSame(4, $obj4->toType('int'));
        $this->assertSame(null, $obj4->toType('unset'));
        $this->assertSame(null, $obj4->toType('null'));
    }

    public function testValueWrapper2() {
        foreach ([IValueWrapper::class, new ReflectionClass(IValueWrapper::class)] as $targetType) {
            /* @var IValueWrapper $obj1 */
            /* @var IValueWrapper $obj2 */
            /* @var IValueWrapper $obj3 */
            /* @var IValueWrapper $obj4 */

            $val1 = '1';
            $val2 = 2;
            $val3 = 3.0;
            $val4 = 4.5;

            $obj1 = Object::convertTo($val1, $targetType);
            $obj2 = Object::convertTo($val2, $targetType);
            $obj3 = Object::convertTo($val3, $targetType);
            $obj4 = Object::convertTo($val4, $targetType);

            $this->assertInstanceOf(IValueWrapper::class, $obj1);
            $this->assertSame($val1, $obj1->getWrappedValue());

            $this->assertInstanceOf(IValueWrapper::class, $obj2);
            $this->assertSame($val2, $obj2->getWrappedValue());

            $this->assertInstanceOf(IValueWrapper::class, $obj3);
            $this->assertSame($val3, $obj3->getWrappedValue());

            $this->assertInstanceOf(IValueWrapper::class, $obj4);
            $this->assertSame($val4, $obj4->getWrappedValue());
        }
    }
}
