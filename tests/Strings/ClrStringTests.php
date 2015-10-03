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

use \System\ClrString;
use \System\IString;


/**
 * Tests for \System\ClrString class.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class ClrStringTests extends TestCaseBase {
    /**
     * Creates an instance of the \System\ClrString class.
     *
     * @param mixed $value The initial value.
     *
     * @return ClrString The new instance.
     */
    protected function createInstance($value = '') {
        return new ClrString($value);
    }

    protected function checkTransformMethod(callable $action, $expected, $initialVal = '') {
        /* @var ClrString $str1 */
        /* @var ClrString $str2 */

        $str1 = $initialVal;
        if (!$str1 instanceof ClrString) {
            $str1 = $this->createInstance($str1);
        }

        $str2 = $action($str1);

        $this->assertInstanceOf(ClrString::class, $str1);
        $this->assertInstanceOf(ClrString::class, $str2);

        $this->assertNotSame($str1, $str2);

        $this->assertSame($expected, (string)$str2);
        $this->assertSame($expected, $str2->getWrappedValue());

        $this->assertFalse($str2->isMutable());

        return $str2;
    }

    public function testAsMutable() {
        $strs   = [];
        $strs[] = $this->createInstance(null);
        $strs[] = $this->createInstance('');
        $strs[] = $this->createInstance('ABC');
        $strs[] = $this->createInstance('  ABC   ');

        foreach ($strs as $s1) {
            /* @var ClrString $s1 */
            $s2 = $s1->asMutable();

            $this->assertFalse($s1->isMutable());
            $this->assertTrue($s2->isMutable());
        }
    }

    public function testContainsString() {
        $str = $this->createInstance('abcdef');

        foreach (['a', 'b', 'c', 'd', 'e', 'f'] as $char) {
            $this->assertTrue($str->containsString($char));
            $this->assertFalse($str->containsString(strtoupper($char)));
        }

        foreach (['a', 'b', 'c', 'd', 'e', 'f'] as $char) {
            $this->assertTrue($str->containsString($char), true);
            $this->assertTrue($str->containsString(strtoupper($char), true));
        }

        $this->assertFalse($str->containsString('a', 1));
        $this->assertFalse($str->containsString('A', true, 1));

        foreach (['b', 'c', 'd', 'e', 'f'] as $char) {
            $this->assertTrue($str->containsString($char, 1));
            $this->assertFalse($str->containsString(strtoupper($char), 1));
        }

        foreach (['b', 'c', 'd', 'e', 'f'] as $char) {
            $this->assertTrue($str->containsString($char, true, 1));
            $this->assertTrue($str->containsString(strtoupper($char), true, 1));
        }
    }

    public function testEndsWith() {
        $str = $this->createInstance('ABCDE');

        $this->assertTrue($str->endsWith('E'));
        $this->assertTrue($str->endsWith('DE'));
        $this->assertFalse($str->endsWith('D'));
        $this->assertFalse($str->endsWith('CD'));
        $this->assertFalse($str->endsWith('C'));
        $this->assertFalse($str->endsWith('BC'));
        $this->assertFalse($str->endsWith('B'));
        $this->assertFalse($str->endsWith('AB'));
        $this->assertFalse($str->endsWith('A'));

        $this->assertFalse($str->endsWith('e'));
        $this->assertTrue($str->endsWith('e', true));
    }

    public function testInvoke() {
        $now = new DateTime();

        $str = $this->createInstance('Hello, {1}{0} It is {2:Y-m-d H:i:s}.');

        $this->assertSame('Hello, JS! It is ' . $now->format('Y-m-d H:i:s') . '.', (string)$str('!', 'JS', $now));
        $this->assertSame('Hello, JS! It is ' . $now->format('Y-m-d H:i:s') . '.', $str('!', 'JS', $now)->getWrappedValue());
    }

    public function testIsMutable() {
        $strs   = [];
        $strs[] = $this->createInstance(null);
        $strs[] = $this->createInstance('');
        $strs[] = $this->createInstance('ABC');
        $strs[] = $this->createInstance('  ABC   ');

        foreach ($strs as $s) {
            /* @var ClrString $s */

            $this->assertFalse($s->isMutable());
        }
    }

    public function testIsWhitespace() {
        $str1 = $this->createInstance(null);
        $str2 = $this->createInstance('');
        $str3 = $this->createInstance('ABC');
        $str4 = $this->createInstance('  ABC   ');

        $this->assertTrue($str1->isWhitespace());
        $this->assertTrue($str2->isWhitespace());
        $this->assertFalse($str3->isWhitespace());
        $this->assertFalse($str4->isWhitespace());
    }

    public function testLength() {
        $str1 = $this->createInstance(null);
        $str2 = $this->createInstance('');
        $str3 = $this->createInstance('ABC');

        $this->assertSame(0, $str1->length());
        $this->assertSame(0, $str2->length());
        $this->assertSame(3, $str3->length());
    }

    public function testPad() {
        $this->checkTransformMethod(function(IString $str) {
            return $str->pad(10);
        }, '    12    ', '12');

        $this->checkTransformMethod(function(IString $str) {
            return $str->pad(10, 'x');
        }, 'xxxxABxxxx', 'AB');
    }

    public function testPadLeft() {
        $this->checkTransformMethod(function(IString $str) {
            return $str->padLeft(10);
        }, '        12', '12');

        $this->checkTransformMethod(function(IString $str) {
            return $str->padLeft(10, 'x');
        }, 'xxxxxxxxAB', 'AB');
    }

    public function testPadRight() {
        $this->checkTransformMethod(function(IString $str) {
            return $str->padRight(10);
        }, '12        ', '12');

        $this->checkTransformMethod(function(IString $str) {
            return $str->padRight(10, 'x');
        }, 'ABxxxxxxxx', 'AB');
    }

    public function testSplit() {
        $str1 = $this->createInstance('A B c D eF ');

        $items1 = static::sequenceToArray($str1->split(' '));

        $this->assertEquals(6, count($items1));
        foreach (['A', 'B', 'c', 'D', 'eF', ''] as $index => $expected) {
            $this->assertTrue(isset($items1[$index]));
            $this->assertInstanceOf(get_class($str1), $items1[$index]);
            $this->assertSame($expected, (string)$items1[$index]);
            $this->assertSame($expected, $items1[$index]->getWrappedValue());
        }

        $str2 = $this->createInstance('a-=-B-=-C-=-D-=-eF-=- ');

        $items2 = static::sequenceToArray($str2->split('-=-', 3));

        $this->assertEquals(3, count($items2));
        foreach (['a', 'B', 'C-=-D-=-eF-=- '] as $index => $expected) {
            $this->assertTrue(isset($items2[$index]));
            $this->assertInstanceOf(get_class($str2), $items2[$index]);
            $this->assertSame($expected, (string)$items2[$index]);
            $this->assertSame($expected, $items2[$index]->getWrappedValue());
        }
    }

    public function testStartsWith() {
        $str = $this->createInstance('ABCDE');

        $this->assertTrue($str->startsWith('A'));
        $this->assertTrue($str->startsWith('AB'));
        $this->assertFalse($str->startsWith('Ab'));
        $this->assertFalse($str->startsWith('aB'));
        $this->assertFalse($str->startsWith('ab'));
        $this->assertFalse($str->startsWith('B'));
        $this->assertFalse($str->startsWith('BC'));
        $this->assertFalse($str->startsWith('C'));
        $this->assertFalse($str->startsWith('CD'));
        $this->assertFalse($str->startsWith('D'));
        $this->assertFalse($str->startsWith('DE'));

        $this->assertFalse($str->startsWith('a'));
        $this->assertTrue($str->startsWith('a', true));
    }

    public function testSubString() {
        $this->checkTransformMethod(function(IString $str) {
            return $str->subString(1);
        }, '123456789', '0123456789');

        $this->checkTransformMethod(function(IString $str) {
            return $str->subString(2, 3);
        }, '234', '0123456789');
    }

    public function testToLower() {
        $this->checkTransformMethod(function(IString $str) {
            return $str->toLower();
        }, ' a b  c  ', ' A b  C  ');
    }

    public function testToUpper() {
        $this->checkTransformMethod(function(IString $str) {
            return $str->toUpper();
        }, ' A B  C  ', ' A b  C  ');
    }

    public function testTrim() {
        $this->checkTransformMethod(function(IString $str) {
            return $str->trim();
        }, 'A b  C', ' A b  C  ');
    }

    public function testTrimEnd() {
        $this->checkTransformMethod(function(IString $str) {
            return $str->trimEnd();
        }, ' A b  C', ' A b  C  ');
    }

    public function testTrimStart() {
        $this->checkTransformMethod(function(IString $str) {
            return $str->trimStart();
        }, 'A b  C  ', ' A b  C  ');
    }
}
