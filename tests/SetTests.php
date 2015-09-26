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

use \System\Collections\ISet;
use \System\Collections\Set;


function equalityComparerFunc($x, $y): bool {
    return $x === $y;
}

class EqualityComparerClass {
    public function __invoke($x, $y) {
        return equalityComparerFunc($x, $y);
    }
}

/**
 * Tests for \System\Collections\Set class.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class SetTests extends TestCaseBase {
    /**
     * Checks a set if it has an expected list of exactly same values (in the same order).
     *
     * @param ISet $s The set.
     * @param array $expected The expected values.
     */
    protected function checkExpected(ISet $s, array $expected = array()) {
        foreach ($expected as $index => $e) {
            $s->reset();

            $count = $index;
            while ($count-- > 0 && $s->valid()) {
                $s->next();
            }

            $this->assertSame($e, $s->current());
        }
    }

    /**
     * Creates the equality comparers for the tests.
     *
     * @return array The equality comparers.
     */
    protected function createEqualityComparers() : array {
        return [
            function($x, $y) {
                return equalityComparerFunc($x, $y);
            },
            'equalityComparerFunc',
            '\equalityComparerFunc',
            new EqualityComparerClass(),
            array($this, 'equalityComparerMethod1'),
            array(static::class, 'equalityComparerMethod2'),
            '$x, $y => equalityComparerFunc($x, $y)',
            '($x, $y) => equalityComparerFunc($x, $y)',
            '$x, $y => return equalityComparerFunc($x, $y);',
            '($x, $y) => return equalityComparerFunc($x, $y);',
            '$x, $y => { return equalityComparerFunc($x, $y); }',
            '($x, $y) => { return equalityComparerFunc($x, $y); }',
            '$x, $y => {
return equalityComparerFunc($x, $y);
}',
            '($x, $y) => {
return equalityComparerFunc($x, $y);
}',
        ];
    }

    public function equalityComparerMethod1($x, $y) {
        return equalityComparerFunc($x, $y);
    }

    public static function equalityComparerMethod2($x, $y) {
        return equalityComparerFunc($x, $y);
    }

    public function testAdd() {
        $s = new Set();

        $this->assertEquals(0, count($s));

        $s->add(1);
        $this->assertEquals(1, count($s));

        $s->add(1);
        $this->assertEquals(1, count($s));

        $s->add('1');
        $this->assertEquals(1, count($s));

        $s->add(3);
        $this->assertEquals(2, count($s));
    }

    public function testAddWithEqualityComparer() {
        foreach ($this->createEqualityComparers() as $equalityComparer) {
            $s = new Set(null, $equalityComparer);

            $this->assertEquals(0, count($s));

            $s->add(1);
            $this->assertEquals(1, count($s));
            $this->checkExpected($s, [1]);

            $s->add(1);
            $this->assertEquals(1, count($s));
            $this->checkExpected($s, [1]);

            $s->add('1');
            $this->assertEquals(2, count($s));
            $this->checkExpected($s, [1, '1']);

            $s->add(3.0);
            $this->assertEquals(3, count($s));
            $this->checkExpected($s, [1, '1', 3.0]);
        }
    }

    public function testClear() {
        $s = new Set([1, 2, 3]);

        $this->assertEquals(3, count($s));

        $s->clear();

        $this->assertEquals(0, count($s));
    }

    public function testContainsItem() {
        $s = new Set([1.4, 2, '3']);

        $this->assertEquals(3, count($s));

        $this->assertTrue($s->containsItem(1.4));
        $this->assertTrue($s->containsItem('1.4'));

        $this->assertTrue($s->containsItem(2));
        $this->assertTrue($s->containsItem(2.0));
        $this->assertTrue($s->containsItem('2'));

        $this->assertTrue($s->containsItem(3));
        $this->assertTrue($s->containsItem(3.0));
        $this->assertTrue($s->containsItem('3'));

        $this->assertFalse($s->containsItem(5));
        $this->assertFalse($s->containsItem(5.0));
        $this->assertFalse($s->containsItem('5'));
    }

    public function testContainsItemWithEqualityComparer() {
        foreach ($this->createEqualityComparers() as $equalityComparer) {
            $s = new Set([1.4, 2, '3'], $equalityComparer);

            $this->assertEquals(3, count($s));

            $this->assertTrue($s->containsItem(1.4));
            $this->assertFalse($s->containsItem('1.4'));

            $this->assertTrue($s->containsItem(2));
            $this->assertFalse($s->containsItem(2.0));
            $this->assertFalse($s->containsItem('2'));

            $this->assertFalse($s->containsItem(3));
            $this->assertFalse($s->containsItem(3.0));
            $this->assertTrue($s->containsItem('3'));

            $this->assertFalse($s->containsItem(5));
            $this->assertFalse($s->containsItem(5.0));
            $this->assertFalse($s->containsItem('5'));
        }
    }

    public function testRemove() {
        $s = new Set([1.4, 2, '3', '2']);

        $this->assertEquals(3, count($s));
        $this->checkExpected($s, [1.4, 2, '3']);

        $this->assertTrue($s->remove('2'));
        $this->assertEquals(2, count($s));
        $this->checkExpected($s, [1.4, '3']);

        $this->assertTrue($s->remove(3));
        $this->assertEquals(1, count($s));
        $this->checkExpected($s, [1.4]);

        $this->assertFalse($s->remove(3.0));
        $this->assertEquals(1, count($s));
        $this->checkExpected($s, [1.4]);

        $this->assertFalse($s->remove(5));
        $this->assertEquals(1, count($s));
        $this->checkExpected($s, [1.4]);
    }

    public function testRemoveWithEqualityComparer() {
        foreach ($this->createEqualityComparers() as $equalityComparer) {
            $s = new Set([1.4, 2, '3', '2'], $equalityComparer);

            $this->assertEquals(4, count($s));
            $this->checkExpected($s, [1.4, 2, '3', '2']);

            $this->assertTrue($s->remove('2'));
            $this->assertEquals(3, count($s));
            $this->checkExpected($s, [1.4, 2, '3']);

            $this->assertFalse($s->remove(3));
            $this->assertEquals(3, count($s));
            $this->checkExpected($s, [1.4, 2, '3']);

            $this->assertFalse($s->remove(5));
            $this->assertEquals(3, count($s));
            $this->checkExpected($s, [1.4, 2, '3']);
        }
    }
}
