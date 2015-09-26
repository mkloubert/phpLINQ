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

use \System\ArgumentOutOfRangeException;
use \System\Collections\Collection;


function equalityComparer1Func($x, $y) : bool {
    return 0 === strcasecmp(trim($x), trim($y));
}

class EqualityComparer1Class {
    public function __invoke($x, $y) {
        return equalityComparer1Func($x, $y);
    }
}

/**
 * Tests for \System\Collections\Collection class.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class CollectionTests extends TestCaseBase {
    /**
     * Creates the equality comparers for the tests
     * that checks strings case insensitive.
     *
     * @return mixed The equality comparers.
     */
    protected function createEqualityComparers1() : array {
        return [
            function ($x, $y) {
                return equalityComparer1Func($x, $y);
            },
            'equalityComparer1Func',
            '\equalityComparer1Func',
            new EqualityComparer1Class(),
            array($this, 'equalityComparer1Method1'),
            array(static::class, 'equalityComparer1Method2'),
            '$x, $y => equalityComparer1Func($x, $y)',
            '($x, $y) => equalityComparer1Func($x, $y)',
            '$x, $y => return equalityComparer1Func($x, $y);',
            '($x, $y) => return equalityComparer1Func($x, $y);',
            '$x, $y => { return equalityComparer1Func($x, $y); }',
            '($x, $y) => { return equalityComparer1Func($x, $y); }',
            '$x, $y => {
return equalityComparer1Func($x, $y);
}',
            '($x, $y) => {
return equalityComparer1Func($x, $y);
}',
        ];
    }

    public function equalityComparer1Method1($x, $y) {
        return equalityComparer1Func($x, $y);
    }

    public static function equalityComparer1Method2($x, $y) {
        return equalityComparer1Func($x, $y);
    }

    public function testAdd() {
        $coll = new Collection();

        $this->assertEquals(0, count($coll));

        $coll->add(11);
        $this->assertEquals(1, count($coll));
        $this->assertTrue(isset($coll[0]));
        $this->assertSame(11, $coll[0]);

        $coll->add(2.34);
        $this->assertEquals(2, count($coll));
        $this->assertTrue(isset($coll[0]));
        $this->assertSame(11, $coll[0]);
        $this->assertTrue(isset($coll[1]));
        $this->assertSame(2.34, $coll[1]);

        $coll->add('5');
        $this->assertEquals(3, count($coll));
        $this->assertTrue(isset($coll[0]));
        $this->assertSame(11, $coll[0]);
        $this->assertTrue(isset($coll[1]));
        $this->assertSame(2.34, $coll[1]);
        $this->assertTrue(isset($coll[2]));
        $this->assertSame('5', $coll[2]);
    }

    public function testAddItems() {
        $coll = new Collection();

        $this->assertEquals(0, count($coll));

        $coll->addItems(1, '2.34', 5.0);
        $this->assertEquals(3, count($coll));
        $this->assertTrue(isset($coll[0]));
        $this->assertSame(1, $coll[0]);
        $this->assertTrue(isset($coll[1]));
        $this->assertSame('2.34', $coll[1]);
        $this->assertTrue(isset($coll[2]));
        $this->assertSame(5.0, $coll[2]);

        $coll->addItems();
        $this->assertEquals(3, count($coll));
        $this->assertTrue(isset($coll[0]));
        $this->assertSame(1, $coll[0]);
        $this->assertTrue(isset($coll[1]));
        $this->assertSame('2.34', $coll[1]);
        $this->assertTrue(isset($coll[2]));
        $this->assertSame(5.0, $coll[2]);

        $obj = new stdClass();
        $obj->value = 'Blaukraut bleibt Blaukraut und Brautkleid bleibt Brautkleid';

        $coll->addItems($obj);
        $this->assertEquals(4, count($coll));
        $this->assertTrue(isset($coll[0]));
        $this->assertSame(1, $coll[0]);
        $this->assertTrue(isset($coll[1]));
        $this->assertSame('2.34', $coll[1]);
        $this->assertTrue(isset($coll[2]));
        $this->assertSame(5.0, $coll[2]);
        $this->assertTrue(isset($coll[3]));
        $this->assertInstanceOf(stdClass::class, $coll[3]);
        $this->assertSame('Blaukraut bleibt Blaukraut und Brautkleid bleibt Brautkleid', $coll[3]->value);
    }

    public function testAddRange() {
        $createGenerator = function() {
            yield 4.0;
            yield 'five';
        };

        $coll = new Collection();

        $this->assertEquals(0, count($coll));

        $coll->addRange();
        $this->assertEquals(0, count($coll));

        $coll->addRange([1, 2]);
        $this->assertEquals(2, count($coll));
        $this->assertTrue(isset($coll[0]));
        $this->assertSame(1, $coll[0]);
        $this->assertTrue(isset($coll[1]));
        $this->assertSame(2, $coll[1]);

        $coll->addRange(new ArrayIterator(['3']));
        $this->assertEquals(3, count($coll));
        $this->assertTrue(isset($coll[0]));
        $this->assertSame(1, $coll[0]);
        $this->assertTrue(isset($coll[1]));
        $this->assertSame(2, $coll[1]);
        $this->assertTrue(isset($coll[2]));
        $this->assertSame('3', $coll[2]);

        $coll->addRange($createGenerator());
        $this->assertEquals(5, count($coll));
        $this->assertTrue(isset($coll[0]));
        $this->assertSame(1, $coll[0]);
        $this->assertTrue(isset($coll[1]));
        $this->assertSame(2, $coll[1]);
        $this->assertTrue(isset($coll[2]));
        $this->assertSame('3', $coll[2]);
        $this->assertTrue(isset($coll[3]));
        $this->assertSame(4.0, $coll[3]);
        $this->assertTrue(isset($coll[4]));
        $this->assertSame('five', $coll[4]);
    }

    public function testArrayAccess() {
        $coll = new Collection(['a']);

        $this->assertEquals(1, count($coll));
        $this->assertFalse(isset($coll[1]));

        try {
            $item1 = $coll[1];
        }
        catch (ArgumentOutOfRangeException $ex) {
            $thrownEx1 = $ex;
        }
        $this->assertFalse(isset($item1));
        $this->assertTrue(isset($thrownEx1));
        $this->assertInstanceOf(ArgumentOutOfRangeException::class, $thrownEx1);

        unset($item1);
        unset($thrownEx1);

        $coll->add(1);

        $this->assertEquals(2, count($coll));
        $this->assertTrue(isset($coll[1]));

        try {
            $item1 = $coll[1];
        }
        catch (ArgumentOutOfRangeException $ex) {
            $thrownEx1 = $ex;
        }

        $this->assertTrue(isset($item1));
        $this->assertSame(1, $item1);
        $this->assertFalse(isset($thrownEx1));

        unset($item1);
        unset($thrownEx1);

        unset($coll[0]);

        $this->assertEquals(1, count($coll));

        try {
            $item1 = $coll[1];
        }
        catch (ArgumentOutOfRangeException $ex) {
            $thrownEx1 = $ex;
        }
        $this->assertFalse(isset($item1));
        $this->assertTrue(isset($thrownEx1));
        $this->assertInstanceOf(ArgumentOutOfRangeException::class, $thrownEx1);

        unset($thrownEx1);

        $this->assertTrue(isset($coll[0]));
        $this->assertFalse(isset($coll[1]));
        $this->assertSame(1, $coll[0]);

        $coll[0] = 'one';
        $this->assertEquals(1, count($coll));
        $this->assertTrue(isset($coll[0]));
        $this->assertSame('one', $coll[0]);

        try {
            $coll[1] = 'ToToTo';
        }
        catch (ArgumentOutOfRangeException $ex) {
            $thrownEx1 = $ex;
        }
        $this->assertEquals(1, count($coll));
        $this->assertFalse(isset($coll[1]));
        $this->assertTrue(isset($thrownEx1));
        $this->assertInstanceOf(ArgumentOutOfRangeException::class, $thrownEx1);

        $coll[] = 'tOtO';
        $this->assertEquals(2, count($coll));
        $this->assertTrue(isset($coll[1]));
        $this->assertSame('tOtO', $coll[1]);
    }

    public function testClear() {
        $coll = new Collection([1, '2', 3.5]);

        $this->assertEquals(3, count($coll));
        $this->assertTrue(isset($coll[0]));
        $this->assertSame(1, $coll[0]);
        $this->assertTrue(isset($coll[1]));
        $this->assertSame('2', $coll[1]);
        $this->assertTrue(isset($coll[2]));
        $this->assertSame(3.5, $coll[2]);

        $coll->clear();
        $this->assertEquals(0, count($coll));
        $this->assertFalse(isset($coll[0]));
        $this->assertFalse(isset($coll[1]));
        $this->assertFalse(isset($coll[2]));
    }

    public function testContainsItem1() {
        $coll = new Collection([1, '2', 3.5]);

        $this->assertEquals(3, count($coll));

        $this->assertTrue($coll->containsItem(1));
        $this->assertTrue($coll->containsItem(1.0));
        $this->assertTrue($coll->containsItem('1'));

        $this->assertTrue($coll->containsItem(2));
        $this->assertTrue($coll->containsItem(2.0));
        $this->assertTrue($coll->containsItem('2'));

        $this->assertTrue($coll->containsItem(3.5));
        $this->assertTrue($coll->containsItem('3.5'));

        $this->assertFalse($coll->containsItem(4));
        $this->assertFalse($coll->containsItem(4.0));
        $this->assertFalse($coll->containsItem('4'));
    }

    public function testContainsItem2() {
        foreach ($this->createEqualityComparers1() as $equalityComparer) {
            $coll = new Collection(['a', 'B', 'c'], $equalityComparer);

            $this->assertEquals(3, count($coll));

            $this->assertTrue($coll->containsItem('a'));
            $this->assertTrue($coll->containsItem('A'));

            $this->assertTrue($coll->containsItem('b'));
            $this->assertTrue($coll->containsItem('B'));

            $this->assertTrue($coll->containsItem('c'));
            $this->assertTrue($coll->containsItem('C'));

            $this->assertFalse($coll->containsItem('d'));
            $this->assertFalse($coll->containsItem('D'));

            $this->assertFalse($coll->containsItem(''));
            $this->assertFalse($coll->containsItem(''));

            $this->assertFalse($coll->containsItem(null));
            $this->assertFalse($coll->containsItem(null));
        }
    }

    public function testInsertIndexOf1() {
        $coll = new Collection([1, '2', 3.5]);

        $this->assertEquals(3, count($coll));

        $this->assertEquals(0, $coll->indexOf(1));
        $this->assertEquals(0, $coll->indexOf(1.0));
        $this->assertEquals(0, $coll->indexOf('1'));

        $this->assertEquals(1, $coll->indexOf(2));
        $this->assertEquals(1, $coll->indexOf(2.0));
        $this->assertEquals(1, $coll->indexOf('2'));

        $this->assertEquals(2, $coll->indexOf(3.5));
        $this->assertEquals(2, $coll->indexOf('3.5'));

        $this->assertEquals(-1, $coll->indexOf(4));
        $this->assertEquals(-1, $coll->indexOf(4.0));
        $this->assertEquals(-1, $coll->indexOf('4'));
    }

    public function testInsertIndexOf2() {
        foreach ($this->createEqualityComparers1() as $equalityComparer) {
            $coll = new Collection(['A', 'B', 'c', 'a', 'b'], $equalityComparer);

            $this->assertEquals(5, count($coll));

            $this->assertEquals(0, $coll->indexOf('a'));
            $this->assertEquals(0, $coll->indexOf('A'));

            $this->assertEquals(1, $coll->indexOf('b'));
            $this->assertEquals(1, $coll->indexOf('B'));

            $this->assertEquals(2, $coll->indexOf('c'));
            $this->assertEquals(2, $coll->indexOf('C'));

            $this->assertEquals(-1, $coll->indexOf('d'));
            $this->assertEquals(-1, $coll->indexOf('D'));

            $this->assertEquals(-1, $coll->indexOf(''));
            $this->assertEquals(-1, $coll->indexOf(''));

            $this->assertEquals(-1, $coll->indexOf(null));
            $this->assertEquals(-1, $coll->indexOf(null));
        }
    }

    public function testInsert() {
        $coll = new Collection([1, '2', 3.5]);

        $this->assertEquals(3, count($coll));

        $coll->insert(0, null);
        $this->assertEquals(4, count($coll));
        $this->assertTrue(isset($coll[0]));
        $this->assertSame(null, $coll[0]);
        $this->assertTrue(isset($coll[1]));
        $this->assertSame(1, $coll[1]);
        $this->assertTrue(isset($coll[2]));
        $this->assertSame('2', $coll[2]);
        $this->assertTrue(isset($coll[3]));
        $this->assertSame(3.5, $coll[3]);

        $coll->insert(2, 'AbC');
        $this->assertEquals(5, count($coll));
        $this->assertTrue(isset($coll[0]));
        $this->assertSame(null, $coll[0]);
        $this->assertTrue(isset($coll[1]));
        $this->assertSame(1, $coll[1]);
        $this->assertTrue(isset($coll[2]));
        $this->assertSame('AbC', $coll[2]);
        $this->assertTrue(isset($coll[3]));
        $this->assertSame('2', $coll[3]);
        $this->assertTrue(isset($coll[4]));
        $this->assertSame(3.5, $coll[4]);

        $coll->insert(5, 'Wurst');
        $this->assertEquals(6, count($coll));
        $this->assertTrue(isset($coll[0]));
        $this->assertSame(null, $coll[0]);
        $this->assertTrue(isset($coll[1]));
        $this->assertSame(1, $coll[1]);
        $this->assertTrue(isset($coll[2]));
        $this->assertSame('AbC', $coll[2]);
        $this->assertTrue(isset($coll[3]));
        $this->assertSame('2', $coll[3]);
        $this->assertTrue(isset($coll[4]));
        $this->assertSame(3.5, $coll[4]);
        $this->assertTrue(isset($coll[5]));
        $this->assertSame('Wurst', $coll[5]);
    }

    public function testRemove1() {
        $coll = new Collection([1, 2.3, '4']);

        $this->assertEquals(3, count($coll));
        $this->assertTrue(isset($coll[0]));
        $this->assertSame(1, $coll[0]);
        $this->assertTrue(isset($coll[1]));
        $this->assertSame(2.3, $coll[1]);
        $this->assertTrue(isset($coll[2]));
        $this->assertSame('4', $coll[2]);

        $this->assertTrue($coll->remove(1));
        $this->assertEquals(2, count($coll));
        $this->assertTrue(isset($coll[0]));
        $this->assertSame(2.3, $coll[0]);
        $this->assertTrue(isset($coll[1]));
        $this->assertSame('4', $coll[1]);
        $this->assertFalse(isset($coll[2]));

        $this->assertFalse($coll->remove(5));
        $this->assertEquals(2, count($coll));
        $this->assertTrue(isset($coll[0]));
        $this->assertSame(2.3, $coll[0]);
        $this->assertTrue(isset($coll[1]));
        $this->assertSame('4', $coll[1]);

        $this->assertTrue($coll->remove('4'));
        $this->assertEquals(1, count($coll));
        $this->assertTrue(isset($coll[0]));
        $this->assertSame(2.3, $coll[0]);
        $this->assertFalse(isset($coll[1]));
    }

    public function testRemove2() {
        foreach ($this->createEqualityComparers1() as $equalityComparer) {
            $coll = new Collection(['a', 'b', 'A'], $equalityComparer);

            $this->assertEquals(3, count($coll));
            $this->assertTrue(isset($coll[0]));
            $this->assertSame('a', $coll[0]);
            $this->assertTrue(isset($coll[1]));
            $this->assertSame('b', $coll[1]);
            $this->assertTrue(isset($coll[2]));
            $this->assertSame('A', $coll[2]);

            $this->assertTrue($coll->remove('A'));
            $this->assertEquals(2, count($coll));
            $this->assertTrue(isset($coll[0]));
            $this->assertSame('b', $coll[0]);
            $this->assertTrue(isset($coll[1]));
            $this->assertSame('A', $coll[1]);
        }
    }

    public function testRemoveAt() {
        $coll = new Collection([1.0, 4, 2, '3.6']);

        $this->assertEquals(4, count($coll));
        $this->assertTrue(isset($coll[0]));
        $this->assertSame(1.0, $coll[0]);
        $this->assertTrue(isset($coll[1]));
        $this->assertSame(4, $coll[1]);
        $this->assertTrue(isset($coll[2]));
        $this->assertSame(2, $coll[2]);
        $this->assertTrue(isset($coll[3]));
        $this->assertSame('3.6', $coll[3]);

        try {
            $coll->removeAt(1);
        }
        catch (ArgumentOutOfRangeException $ex) {
            $thrownEx1 = $ex;
        }

        $this->assertEquals(3, count($coll));
        $this->assertTrue(isset($coll[0]));
        $this->assertSame(1.0, $coll[0]);
        $this->assertTrue(isset($coll[1]));
        $this->assertSame(2, $coll[1]);
        $this->assertTrue(isset($coll[2]));
        $this->assertSame('3.6', $coll[2]);
        $this->assertFalse(isset($thrownEx1));

        try {
            $coll->removeAt(2);
        }
        catch (ArgumentOutOfRangeException $ex) {
            $thrownEx1 = $ex;
        }

        $this->assertEquals(2, count($coll));
        $this->assertTrue(isset($coll[0]));
        $this->assertSame(1.0, $coll[0]);
        $this->assertTrue(isset($coll[1]));
        $this->assertSame(2, $coll[1]);
        $this->assertFalse(isset($thrownEx1));

        try {
            $coll->removeAt(2);
        }
        catch (ArgumentOutOfRangeException $ex) {
            $thrownEx1 = $ex;
        }

        $this->assertEquals(2, count($coll));
        $this->assertTrue(isset($coll[0]));
        $this->assertSame(1.0, $coll[0]);
        $this->assertTrue(isset($coll[1]));
        $this->assertSame(2, $coll[1]);
        $this->assertTrue(isset($thrownEx1));
        $this->assertInstanceOf(ArgumentOutOfRangeException::class, $thrownEx1);
    }
}
