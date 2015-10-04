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

use \System\EqualityComparer;


function equalityComparerClassEqualityComparerFunc($x, $y) : bool {
    return $x === $y;
}

class equalityComparerClassEqualityComparerClass {
    public function __invoke($x, $y) {
        return equalityComparerClassEqualityComparerFunc($x, $y);
    }
}

/**
 * Tests for \System\EqualityComparer class.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class EqualityComparerTests extends TestCaseBase {
    /**
     * Creates the class reflector for the tests.
     *
     * @return ReflectionClass The reflector.
     */
    protected function createClassReflector() : ReflectionClass {
        return new ReflectionClass(EqualityComparer::class);
    }

    /**
     * Creates the comparer objects for the tests.
     *
     * @return array The created objects.
     */
    protected function createComparerObjects() : array {
        return [
            $this->createInstance(3),
            $this->createInstance(2),
            $this->createInstance(4),
            $this->createInstance(1),
        ];
    }

    /**
     * Creates the equality comparers for the tests.
     *
     * @return array The equality comparers.
     */
    protected function createEqualityComparers() : array {
        return [
            true,
            'equalityComparerClassEqualityComparerFunc',
            '\equalityComparerClassEqualityComparerFunc',
            new equalityComparerClassEqualityComparerClass(),
            function ($x, $y) {
                return equalityComparerClassEqualityComparerFunc($x, $y);
            },
            [$this, 'equalityComparerMethod1'],
            [static::class, 'equalityComparerMethod2'],
            '$x, $y => equalityComparerClassEqualityComparerFunc($x, $y)',
            '$x, $y => \equalityComparerClassEqualityComparerFunc($x, $y)',
            '($x, $y) => equalityComparerClassEqualityComparerFunc($x, $y)',
            '($x, $y) => \equalityComparerClassEqualityComparerFunc($x, $y)',
            '$x, $y => return equalityComparerClassEqualityComparerFunc($x, $y);',
            '$x, $y => return \equalityComparerClassEqualityComparerFunc($x, $y);',
            '($x, $y) => return equalityComparerClassEqualityComparerFunc($x, $y);',
            '($x, $y) => return \equalityComparerClassEqualityComparerFunc($x, $y);',
            '$x, $y => { return equalityComparerClassEqualityComparerFunc($x, $y); }',
            '$x, $y => { return \equalityComparerClassEqualityComparerFunc($x, $y); }',
            '($x, $y) => { return equalityComparerClassEqualityComparerFunc($x, $y); }',
            '($x, $y) => { return \equalityComparerClassEqualityComparerFunc($x, $y); }',
            '$x, $y => {
                 return equalityComparerClassEqualityComparerFunc($x, $y);
             }',
            '$x, $y => {
                 return \equalityComparerClassEqualityComparerFunc($x, $y);
             }',
            '($x, $y) => {
                 return equalityComparerClassEqualityComparerFunc($x, $y);
             }',
            '($x, $y) => {
                 return \equalityComparerClassEqualityComparerFunc($x, $y);
             }',
        ];
    }

    /**
     * Creates an instance of a \System\EqualityComparer based class.
     *
     * @param mixed $value The value to wrap.
     * @param callable $equalityComparer The custom equality comparer.
     * @param callable $comparer The custom comparer.
     *
     * @return EqualityComparer The new instance.
     */
    protected function createInstance($value, $equalityComparer = null, $comparer = null) {
        return $this->createClassReflector()
                    ->newInstance($value, $equalityComparer);
    }

    public function equalityComparerMethod1($x, $y) {
        return static::equalityComparerMethod2($x, $y);
    }

    public static function equalityComparerMethod2($x, $y) {
        return equalityComparerClassEqualityComparerFunc($x, $y);
    }

    public function testComparer1() {
        $objs = $this->createComparerObjects();

        usort($objs, function(EqualityComparer $x, EqualityComparer $y) : int {
            return $x->compareTo($y);
        });

        $this->assertEquals(4, count($objs));

        $values = array_map(function(EqualityComparer $obj) {
            return $obj->getWrappedValue();
        }, $objs);

        $this->assertEquals(count($objs), count($values));

        for ($i = 0; $i < count($values); $i++) {
            $this->assertSame($i + 1, $values[$i]);
        }
    }

    public function testComparer2() {
        $objs = $this->createComparerObjects();

        usort($objs, function(EqualityComparer $x, EqualityComparer $y) : int {
            return $y->compareTo($x);
        });

        $this->assertEquals(4, count($objs));

        $values = array_map(function(EqualityComparer $obj) {
            return $obj->getWrappedValue();
        }, $objs);

        $this->assertEquals(count($objs), count($values));

        for ($i = 0; $i < count($values); $i++) {
            $this->assertSame(count($values) - $i, $values[$i]);
        }
    }

    public function testEqualityComparers() {
        foreach ($this->createEqualityComparers() as $equalityComparer) {
            $obj1 = $this->createInstance(1, $equalityComparer);
            $obj2 = $this->createInstance(2, $equalityComparer);
            $obj3 = $this->createInstance('3', $equalityComparer);
            $obj4 = $this->createInstance(1, $equalityComparer);
            $obj5 = $this->createInstance('1', $equalityComparer);
            $obj6 = $this->createInstance(1.0, $equalityComparer);

            $this->assertTrue($obj1->equals($obj1));

            $this->assertFalse($obj1->equals($obj2));
            $this->assertFalse($obj2->equals($obj1));

            $this->assertFalse($obj1->equals($obj3));
            $this->assertFalse($obj3->equals($obj1));

            $this->assertTrue($obj1->equals($obj4));
            $this->assertTrue($obj4->equals($obj1));

            $this->assertFalse($obj1->equals($obj5));
            $this->assertFalse($obj5->equals($obj1));

            $this->assertFalse($obj1->equals($obj6));
            $this->assertFalse($obj6->equals($obj1));
        }
    }
}
