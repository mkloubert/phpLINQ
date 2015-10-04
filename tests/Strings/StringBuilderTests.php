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

require_once __DIR__ . DIRECTORY_SEPARATOR . 'ClrStringTests.php';

use \System\ArgumentOutOfRangeException;
use \System\IMutableString;
use \System\Text\StringBuilder;


/**
 * Tests for \System\Text\StringBuilder class.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class StringBuilderTest extends ClrStringTests {
    /**
     * {@inheritDoc}
     */
    protected function createClassReflector() : ReflectionClass {
        return new ReflectionClass(StringBuilder::class);
    }

    /**
     * {@inheritDoc}
     */
    protected function checkTransformMethod(callable $action, $expected, $initialVal = '') {
        /* @var StringBuilder $str1 */
        /* @var StringBuilder $str2 */

        $str1 = $initialVal;
        if (!$str1 instanceof StringBuilder) {
            $str1 = $this->createInstance($str1);
        }

        $str2 = $action($str1);

        $this->assertInstanceOf(StringBuilder::class, $str1);
        $this->assertInstanceOf(StringBuilder::class, $str2);

        $this->assertSame($str1, $str2);

        $this->assertSame($expected, (string)$str2);
        $this->assertSame($expected, $str2->getWrappedValue());

        $this->assertTrue($str2->isMutable());

        return $str2;
    }

    public function testArrayAccess() {
        $str = $this->createInstance('ABC');

        $this->assertTrue(isset($str[0]));
        $this->assertSame('A', $str[0]);
        $this->assertTrue(isset($str[1]));
        $this->assertSame('B', $str[1]);
        $this->assertTrue(isset($str[2]));
        $this->assertSame('C', $str[2]);

        $this->assertFalse(isset($str[3]));
        try {
            $char = $str[3];
        }
        catch (\Exception $ex) {
            $thrownEx = $ex;
        }

        $this->assertFalse(isset($char));
        $this->assertTrue(isset($thrownEx));
        $this->assertInstanceOf(ArgumentOutOfRangeException::class, $thrownEx);

        unset($char);
        unset($thrownEx);

        $this->checkTransformMethod(function($str) {
            $str[2] = 'cE';

            return $str;
        }, 'ABcE', $str);

        $this->assertEquals(4, count($str));
        $this->assertSame('ABcE', (string)$str);
        $this->assertSame('ABcE', $str->getWrappedValue());

        unset($str[0]);

        $this->assertEquals(3, count($str));
        $this->assertSame('BcE', (string)$str);
        $this->assertSame('BcE', $str->getWrappedValue());
    }

    public function testAsMutable() {
        $strs   = [];
        $strs[] = $this->createInstance(null);
        $strs[] = $this->createInstance('');
        $strs[] = $this->createInstance('ABC');
        $strs[] = $this->createInstance('  ABC   ');

        foreach ($strs as $s1) {
            /* @var StringBuilder $s1 */
            $s2 = $s1->asMutable();

            $this->assertTrue($s1->isMutable());
            $this->assertTrue($s2->isMutable());
            $this->assertSame($s1, $s2);
        }
    }

    public function testClear() {
        $str2 = $this->checkTransformMethod(function(IMutableString $str) {
            return $str->clear();
        }, '', ' A b  C  ');

        $this->assertSame(0, $str2->length());
        $this->assertTrue($str2->isEmpty());
        $this->assertFalse($str2->isNotEmpty());
    }

    public function testIsMutable() {
        $strs   = [];
        $strs[] = $this->createInstance(null);
        $strs[] = $this->createInstance('');
        $strs[] = $this->createInstance('ABC');
        $strs[] = $this->createInstance('  ABC   ');

        foreach ($strs as $s) {
            /* @var StringBuilder $s */

            $this->assertTrue($s->isMutable());
        }
    }
}
