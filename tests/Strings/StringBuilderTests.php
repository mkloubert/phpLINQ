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

use \System\IMutableString;
use \System\Linq\Enumerable;
use \System\Text\StringBuilder;


/**
 * Tests for \System\Text\StringBuilder class.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class StringBuilderTest extends ClrStringTests {
    /**
     * Creates an instance of the \System\Text\StringBuilder class.
     *
     * @param mixed $value The initial value.
     *
     * @return StringBuilder The new instance.
     */
    protected function createInstance($value = '') {
        return new StringBuilder($value);
    }


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

        return $str2;
    }

    public function testAppend() {
        $str1 = $this->createInstance();

        $str2 = $this->checkTransformMethod(function(IMutableString $str) {
            return $str->append(1);
        }, '1', $str1);

        $str3 = $this->checkTransformMethod(function(IMutableString $str) {
            return $str->append('2');
        }, '12', $str2);

        $str4 = $this->checkTransformMethod(function(IMutableString $str) {
            return $str->append(4.0);
        }, '124', $str3);

        $str5 = $this->checkTransformMethod(function(IMutableString $str) {
            return $str->append('TM');
        }, '124TM', $str4);

        $str6 = $this->checkTransformMethod(function(IMutableString $str) {
            return $str->append(4.5, 'seven');
        }, '124TM4.5seven', $str5);
    }

    public function testAppendArray() {
        $createGenerator = function() {
            yield 'MK';
            yield null;
            yield 'TM';
        };

        $str1 = $this->createInstance();

        $str2 = $this->checkTransformMethod(function(IMutableString $str) {
            return $str->appendArray([1.0]);
        }, '1', $str1);

        $str3 = $this->checkTransformMethod(function(IMutableString $str) {
            return $str->appendArray(new ArrayIterator([2, '3']));
        }, '123', $str2);

        $str4 = $this->checkTransformMethod(function(IMutableString $str) use ($createGenerator) {
            return $str->appendArray($createGenerator(),
                                     Enumerable::create($createGenerator())
                                               ->reverse());
        }, '123MKTMTMMK', $str3);
    }

    public function testAppendFormat() {
        $str1 = $this->createInstance();

        $str2 = $this->checkTransformMethod(function(IMutableString $str) {
            return $str->appendFormat('{2}{0}{1}', 1, 2.3, '0');
        }, '012.3', $str1);
    }

    public function testAppendFormatArray() {
        $createGenerator = function() {
            yield 'YS';
            yield 'MK';
            yield null;
        };

        $str1 = $this->createInstance('xyz');

        $str2 = $this->checkTransformMethod(function(IMutableString $str) {
            return $str->appendFormatArray('{2}{0}{3}{1}', [1, 2.0, '3.0', '4']);
        }, 'xyz3.0142', $str1);

        $str3 = $this->checkTransformMethod(function(IMutableString $str) use ($createGenerator) {
            return $str->appendFormatArray('   {0}{1}{2}  {4}{3}{5} ',
                                           $createGenerator(),
                                           Enumerable::create($createGenerator())
                                                     ->select('$x => \strtolower($x)')
                                                     ->reverse());
        }, 'xyz3.0142   YSMK  mkys ', $str2);
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
