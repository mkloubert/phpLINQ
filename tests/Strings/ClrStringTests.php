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
use \System\Linq\Enumerable;


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

    public function testAppend() {
        $str1 = $this->createInstance();

        $str2 = $this->checkTransformMethod(function(IString $str) {
            return $str->append(1);
        }, '1', $str1);

        $str3 = $this->checkTransformMethod(function(IString $str) {
            return $str->append('2');
        }, '12', $str2);

        $str4 = $this->checkTransformMethod(function(IString $str) {
            return $str->append(4.0);
        }, '124', $str3);

        $str5 = $this->checkTransformMethod(function(IString $str) {
            return $str->append('TM');
        }, '124TM', $str4);

        $str6 = $this->checkTransformMethod(function(IString $str) {
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

        $str2 = $this->checkTransformMethod(function(IString $str) {
            return $str->appendArray([1.0]);
        }, '1', $str1);

        $str3 = $this->checkTransformMethod(function(IString $str) {
            return $str->appendArray(new ArrayIterator([2, '3']));
        }, '123', $str2);

        $str4 = $this->checkTransformMethod(function(IString $str) use ($createGenerator) {
            return $str->appendArray($createGenerator(),
                                     Enumerable::create($createGenerator())
                                               ->reverse());
        }, '123MKTMTMMK', $str3);
    }

    public function testAppendFormat() {
        $str1 = $this->createInstance();

        $str2 = $this->checkTransformMethod(function(IString $str) {
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

        $str2 = $this->checkTransformMethod(function(IString $str) {
            return $str->appendFormatArray('{2}{0}{3}{1}', [1, 2.0, '3.0', '4']);
        }, 'xyz3.0142', $str1);

        $str3 = $this->checkTransformMethod(function(IString $str) use ($createGenerator) {
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

    public function testIndexOf() {
        $str = $this->createInstance('012abcdAb345');

        $this->assertSame(3, $str->indexOf('ab'));
        $this->assertSame(7, $str->indexOf('Ab'));
        $this->assertSame(-1, $str->indexOf('aB'));
        $this->assertSame(-1, $str->indexOf('AB'));

        $this->assertSame(3, $str->indexOf('ab', true));
        $this->assertSame(3, $str->indexOf('Ab', true));
        $this->assertSame(3, $str->indexOf('aB', true));
        $this->assertSame(3, $str->indexOf('AB', true));
    }

    public function testInsert() {
        $str1 = $this->createInstance('ABC');

        $str2 = $this->checkTransformMethod(function(IString $str) {
            return $str->insert(1, 'd');
        }, 'AdBC', $str1);

        $str3 = $this->checkTransformMethod(function(IString $str) {
            return $str->insert(2, '-', '?');
        }, 'Ad-?BC', $str2);
    }

    public function testInsertArray() {
        $str1 = $this->createInstance('ABC');

        $str2 = $this->checkTransformMethod(function(IString $str) {
            return $str->insertArray(1, ['d']);
        }, 'AdBC', $str1);

        $str3 = $this->checkTransformMethod(function(IString $str) {
            return $str->insertArray(2, new ArrayIterator(['-', '?']), 'wurst');
        }, 'Adwurst-?BC', $str2);
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

    public function testLastIndexOf() {
        $str = $this->createInstance('012abcdAb345');

        $this->assertSame(3, $str->lastIndexOf('ab'));
        $this->assertSame(7, $str->lastIndexOf('Ab'));
        $this->assertSame(-1, $str->lastIndexOf('aB'));
        $this->assertSame(-1, $str->lastIndexOf('AB'));

        $this->assertSame(7, $str->lastIndexOf('ab', true));
        $this->assertSame(7, $str->lastIndexOf('Ab', true));
        $this->assertSame(7, $str->lastIndexOf('aB', true));
        $this->assertSame(7, $str->lastIndexOf('AB', true));
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

    public function testPrepend() {
        $str1 = $this->createInstance();

        $str2 = $this->checkTransformMethod(function(IString $str) {
            return $str->prepend(1);
        }, '1', $str1);

        $str3 = $this->checkTransformMethod(function(IString $str) {
            return $str->prepend('2');
        }, '21', $str2);

        $str4 = $this->checkTransformMethod(function(IString $str) {
            return $str->prepend(4.0);
        }, '421', $str3);

        $str5 = $this->checkTransformMethod(function(IString $str) {
            return $str->prepend('TM');
        }, 'TM421', $str4);

        $str6 = $this->checkTransformMethod(function(IString $str) {
            return $str->prepend(4.5, 'seven');
        }, '4.5sevenTM421', $str5);
    }

    public function testPrependArray() {
        $createGenerator = function() {
            yield 'MK';
            yield null;
            yield 'TM';
        };

        $str1 = $this->createInstance();

        $str2 = $this->checkTransformMethod(function(IString $str) {
            return $str->prependArray([1.0]);
        }, '1', $str1);

        $str3 = $this->checkTransformMethod(function(IString $str) {
            return $str->prependArray(new ArrayIterator([2, '3']));
        }, '231', $str2);

        $str4 = $this->checkTransformMethod(function(IString $str) use ($createGenerator) {
            return $str->prependArray($createGenerator(),
                                      Enumerable::create($createGenerator())
                                                ->reverse());
        }, 'TMMKMKTM231', $str3);
    }

    public function testPrependFormat() {
        $str1 = $this->createInstance('a');

        $str2 = $this->checkTransformMethod(function(IString $str) {
            return $str->prependFormat('{2}{0}{1}', 1, 2.3, '0');
        }, '012.3a', $str1);
    }

    public function testPrependFormatArray() {
        $createGenerator = function() {
            yield 'ys';
            yield 'Mk';
            yield null;
        };

        $str1 = $this->createInstance('xyz');

        $str2 = $this->checkTransformMethod(function(IString $str) {
            return $str->prependFormatArray('{2}{0}{3}{1}', [1, 2.0, '3.0', '4']);
        }, '3.0142xyz', $str1);

        $str3 = $this->checkTransformMethod(function(IString $str) use ($createGenerator) {
            return $str->prependFormatArray('   {0}{1}{2}  {4}{3}{5} ',
                                            $createGenerator(),
                                            Enumerable::create($createGenerator())
                                                      ->select('$x => \strtoupper($x)')
                                                      ->reverse());
        }, '   ysMk  MKYS 3.0142xyz', $str2);
    }

    public function testReplace() {
        $this->checkTransformMethod(function(IString $str) {
            return $str->replace('CD', '01');
        }, 'ab01efg', 'abCDefg');

        $this->checkTransformMethod(function(IString $str) {
            return $str->replace('cd', '01');
        }, 'abCDefg', 'abCDefg');

        $this->checkTransformMethod(function(IString $str) {
            return $str->replace('cD', '01');
        }, 'abCDefg', 'abCDefg');

        $this->checkTransformMethod(function(IString $str) {
            return $str->replace('Cd', '01');
        }, 'abCDefg', 'abCDefg');

        $this->checkTransformMethod(function(IString $str) {
            return $str->replace('cd', '01', true);
        }, 'ab01efg', 'abCDefg');

        $this->createInstance('abCDefg')
             ->replace('CD', 'cd', false, $replacementCount);
        $this->assertSame(1, $replacementCount);

        $this->createInstance('abCDefg')
             ->replace('cD', 'cd', false, $replacementCount);
        $this->assertSame(0, $replacementCount);

        $this->createInstance('abCDefg')
             ->replace('Cd', 'cd', false, $replacementCount);
        $this->assertSame(0, $replacementCount);

        $this->createInstance('acdbCDefCdcDg')
             ->replace('CD', 'cd', true, $replacementCount);
        $this->assertSame(4, $replacementCount);
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
