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
 * @see \System\Collections\IEnumerable::ofType()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class OfTypeTests extends TestCaseBase {
    public function dummyMethod1() {
    }

    public static function dummyMethod2() {
    }

    public function testArray() {
        foreach (static::sequenceListFromArray([1, [], '2', 3, array('a', 'b'), '4', 5]) as $seq) {
            /* @var IEnumerable $seq */

            $items = static::sequenceToArray($seq->ofType('array'));

            $this->assertEquals(2, count($items));

            foreach ($items as $x) {
                $this->assertTrue('array' === gettype($x));
                $this->assertTrue(is_array($x));
            }
        }
    }

    public function testBool() {
        $values = [
            1,
            "\\trim",
            true,
            function() {},
            null,
            4.5,
            false,
            '$x => null',
        ];

        foreach (static::sequenceListFromArray($values) as $seq) {
            /* @var IEnumerable $seq */

            $items = static::sequenceToArray($seq->ofType('bool'));

            $this->assertEquals(2, count($items));
            $this->assertTrue(true === $items[0]);
            $this->assertTrue(false === $items[1]);

            foreach ($items as $x) {
                $this->assertTrue('boolean' === gettype($x));
                $this->assertTrue(is_bool($x));
            }
        }
    }

    public function testBoolean() {
        $values = [
            1,
            "\\trim",
            false,
            function() {},
            null,
            4.5,
            true,
            '$x => null',
        ];

        foreach (static::sequenceListFromArray($values) as $seq) {
            /* @var IEnumerable $seq */

            $items = static::sequenceToArray($seq->ofType('boolean'));

            $this->assertEquals(2, count($items));
            $this->assertTrue(false === $items[0]);
            $this->assertTrue(true === $items[1]);

            foreach ($items as $x) {
                $this->assertTrue('boolean' === gettype($x));
                $this->assertTrue(is_bool($x));
            }
        }
    }

    public function testCallable() {
        $values = [
            1,
            "\\trim",
            true,
            function() {},
            null,
            array($this, 'dummyMethod1'),
            4.5,
            array(static::class, 'dummyMethod2'),
            '$x => null',
        ];

        foreach (static::sequenceListFromArray($values) as $seq) {
            /* @var IEnumerable $seq */

            $items = static::sequenceToArray($seq->ofType('callable'));

            $this->assertEquals(4, count($items));
            $this->assertTrue("\\trim" === $items[0]);
            $this->assertTrue($items[1] instanceof \Closure);
            $this->assertTrue(is_array($items[2]));
            $this->assertTrue(is_array($items[3]));

            foreach ($items as $x) {
                $this->assertTrue(is_callable($x));
            }
        }
    }

    public function testInt() {
        foreach (static::sequenceListFromArray([1, '2', 3, null, '4', 5, true]) as $seq) {
            /* @var IEnumerable $seq */

            $items = static::sequenceToArray($seq->ofType('int'));

            $this->assertEquals(3, count($items));
            $this->assertTrue(1 === $items[0]);
            $this->assertTrue(3 === $items[1]);
            $this->assertTrue(5 === $items[2]);

            foreach ($items as $x) {
                $this->assertTrue('integer' === gettype($x));
                $this->assertTrue(is_int($x));
                $this->assertTrue(is_integer($x));
            }
        }
    }

    public function testInteger() {
        foreach (static::sequenceListFromArray([1, '2', false, 3, '4', null, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $items = static::sequenceToArray($seq->ofType('integer'));

            $this->assertEquals(3, count($items));
            $this->assertTrue(1 === $items[0]);
            $this->assertTrue(3 === $items[1]);
            $this->assertTrue(5 === $items[2]);

            foreach ($items as $x) {
                $this->assertTrue('integer' === gettype($x));
                $this->assertTrue(is_int($x));
                $this->assertTrue(is_integer($x));
            }
        }
    }

    public function testNull() {
        $values = [
            1,
            new stdClass(),
            null,
            3.141592654,
            false,
            0,
            4,
            new ReflectionObject($this),
            0.0,
        ];

        foreach (static::sequenceListFromArray($values) as $seq) {
            /* @var IEnumerable $seq */

            $items = static::sequenceToArray($seq->ofType('null'));

            $this->assertEquals(1, count($items));

            foreach ($items as $x) {
                $this->assertTrue(null === $x);
                $this->assertTrue(is_null($x));
            }
        }
    }

    public function testObject1() {
        $values = [
            1,
            new stdClass(),
            null,
            3.141592654,
            false,
            function() {},
            4,
            new ReflectionObject($this),
            '5',
        ];

        foreach (static::sequenceListFromArray($values) as $seq) {
            /* @var IEnumerable $seq */

            $items = static::sequenceToArray($seq->ofType('object'));

            $this->assertEquals(3, count($items));
            $this->assertTrue($items[0] instanceof stdClass);
            $this->assertTrue($items[1] instanceof Closure);
            $this->assertTrue($items[2] instanceof ReflectionObject);

            foreach ($items as $x) {
                $this->assertTrue('object' === gettype($x));
                $this->assertTrue(is_object($x));
            }
        }
    }

    public function testObject2() {
        $classNames = [
            'stdClass',
            '\stdClass',
        ];

        foreach ($classNames as $cn) {
            $values = [
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
            ];

            foreach (static::sequenceListFromArray($values) as $seq) {
                /* @var IEnumerable $seq */

                $items = static::sequenceToArray($seq->ofType($cn));

                $this->assertEquals(2, count($items));
                foreach ($items as $x) {
                    $this->assertTrue('object' === gettype($x));
                    $this->assertTrue($x instanceof stdClass);
                    $this->assertTrue(stdClass::class === get_class($x));
                    $this->assertTrue(is_object($x));
                }
            }
        }
    }

    public function testScalar() {
        foreach (static::sequenceListFromArray([1, new stdClass(), '5', 3.141592654, true, 4, new ReflectionObject($this), null]) as $seq) {
            /* @var IEnumerable $seq */

            $items = static::sequenceToArray($seq->ofType('scalar'));

            $this->assertEquals(5, count($items));
            $this->assertTrue(1 === $items[0]);
            $this->assertTrue('5' === $items[1]);
            $this->assertTrue(3.141592654 === $items[2]);
            $this->assertTrue(true === $items[3]);
            $this->assertTrue(4 === $items[4]);

            foreach ($items as $x) {
                $this->assertTrue(is_scalar($x));
            }
        }
    }

    public function testString() {
        foreach (static::sequenceListFromArray([1, null, '2', 3, true, '4', 5]) as $seq) {
            /* @var IEnumerable $seq */

            $items = static::sequenceToArray($seq->ofType('string'));

            $this->assertEquals(2, count($items));
            $this->assertTrue('2' === $items[0]);
            $this->assertTrue('4' === $items[1]);

            foreach ($items as $x) {
                $this->assertTrue('string' === gettype($x));
                $this->assertTrue(is_string($x));
            }
        }
    }
}
