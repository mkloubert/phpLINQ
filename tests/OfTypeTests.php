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
 * @see \System\Collection\IEnumerable::ofType().
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class OfTypeTests extends TestCaseBase {
    public function dummyMethod1() {

    }

    public static function dummyMethod2() {

    }


    public function testArray() {
        $seq = static::sequenceFromArray([1, [], '2', 3, array('a', 'b'), '4', 5]);

        $items = [];
        foreach ($seq->ofType('array') as $x) {
            $items[] = $x;
        }

        $this->assertEquals(2, count($items));
        $this->assertTrue(is_array($items[0]));
        $this->assertTrue('array' === gettype($items[0]));
        $this->assertTrue(is_array($items[1]));
        $this->assertTrue('array' === gettype($items[1]));
    }

    public function testCallable() {
        $seq = static::sequenceFromArray([
            1,
            "\\trim",
            true,
            function() {},
            null,
            array($this, 'dummyMethod1'),
            4.5,
            array(static::class, 'dummyMethod2'),
            '$x => null'
        ]);

        $items = [];
        foreach ($seq->ofType('callable') as $x) {
            $items[] = $x;
        }

        $this->assertEquals(4, count($items));
        $this->assertTrue("\\trim" === $items[0]);
        $this->assertTrue($items[1] instanceof \Closure);
        $this->assertTrue(is_array($items[2]));
        $this->assertTrue(is_array($items[3]));
    }

    public function testInt() {
        $seq = static::sequenceFromArray([1, '2', 3, null, '4', 5, true]);

        $items = [];
        foreach ($seq->ofType('int') as $x) {
            $items[] = $x;
        }

        $this->assertEquals(3, count($items));
        $this->assertTrue(1 === $items[0]);
        $this->assertTrue(3 === $items[1]);
        $this->assertTrue(5 === $items[2]);
    }

    public function testInteger() {
        $seq = static::sequenceFromArray([1, '2', false, 3, '4', null, 5]);

        $items = [];
        foreach ($seq->ofType('integer') as $x) {
            $items[] = $x;
        }

        $this->assertEquals(3, count($items));
        $this->assertTrue(1 === $items[0]);
        $this->assertTrue(3 === $items[1]);
        $this->assertTrue(5 === $items[2]);
    }

    public function testNull() {
        $seq = static::sequenceFromArray([
            1,
            new stdClass(),
            null,
            3.141592654,
            false,
            0,
            4,
            new ReflectionObject($this),
            0.0,
        ]);

        $items = [];
        foreach ($seq->ofType('null') as $x) {
            $items[] = $x;
        }

        $this->assertEquals(1, count($items));
        $this->assertTrue(null === $items[0]);
        $this->assertTrue(is_null($items[0]));
    }

    public function testObject1() {
        $seq = static::sequenceFromArray([
            1,
            new stdClass(),
            null,
            3.141592654,
            false,
            function() {},
            4,
            new ReflectionObject($this),
            '5',
        ]);

        $items = [];
        foreach ($seq->ofType('object') as $x) {
            $items[] = $x;
        }

        $this->assertEquals(3, count($items));
        $this->assertTrue($items[0] instanceof stdClass);
        $this->assertTrue($items[1] instanceof Closure);
        $this->assertTrue($items[2] instanceof ReflectionObject);
    }

    public function testObject2() {
        $classNames = [
            'stdClass',
            '\stdClass',
        ];

        foreach ($classNames as $cn) {
            $seq = static::sequenceFromArray([
                1,
                new stdClass(),
                2,
                new \stdClass(),
                'object',
                'stdClass',
                false,
                4.5,
                new ReflectionObject($this),
                '\stdClass'
            ]);

            $items = [];
            foreach ($seq->ofType($cn) as $x) {
                $items[] = $x;
            }

            $this->assertEquals(2, count($items));
            foreach ($items as $x) {
                $this->assertTrue($x instanceof stdClass);
                $this->assertTrue(stdClass::class === get_class($x));
            }
        }
    }

    public function testScalar() {
        $seq = static::sequenceFromArray([1, new stdClass(), '5', 3.141592654, true, 4, new ReflectionObject($this), null]);

        $items = [];
        foreach ($seq->ofType('scalar') as $x) {
            $items[] = $x;
        }

        $this->assertEquals(5, count($items));
        $this->assertTrue(1 === $items[0]);
        $this->assertTrue('5' === $items[1]);
        $this->assertTrue(3.141592654 === $items[2]);
        $this->assertTrue(true === $items[3]);
        $this->assertTrue(4 === $items[4]);
    }

    public function testString() {
        $seq = static::sequenceFromArray([1, null, '2', 3, true, '4', 5]);

        $items = [];
        foreach ($seq->ofType('string') as $x) {
            $items[] = $x;
        }

        $this->assertEquals(2, count($items));
        $this->assertTrue('2' === $items[0]);
        $this->assertTrue('4' === $items[1]);
    }
}
