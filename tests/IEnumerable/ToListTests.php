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


function toListEqualityComparerFunc($x, $y) : bool {
    return 0 === strcasecmp(trim($x), trim($y));
}

class ToListEqualityComparerClass {
    public function __invoke($x, $y) {
        return toListEqualityComparerFunc($x, $y);
    }
}

/**
 * @see \System\Collections\IEnumerable::toList()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class ToListTests extends TestCaseBase {
    /**
     * Creates the equality comparers for the tests.
     *
     * @return mixed The equality comparers.
     */
    protected function createEqualityComparers() : array {
        return [
            function ($x, $y) {
                return toListEqualityComparerFunc($x, $y);
            },
            'toListEqualityComparerFunc',
            '\toListEqualityComparerFunc',
            new ToListEqualityComparerClass(),
            array($this, 'equalityComparerMethod1'),
            array(static::class, 'equalityComparerMethod2'),
            '$x, $y => toListEqualityComparerFunc($x, $y)',
            '($x, $y) => toListEqualityComparerFunc($x, $y)',
            '$x, $y => return toListEqualityComparerFunc($x, $y);',
            '($x, $y) => return toListEqualityComparerFunc($x, $y);',
            '$x, $y => { return toListEqualityComparerFunc($x, $y); }',
            '($x, $y) => { return toListEqualityComparerFunc($x, $y); }',
            '$x, $y => {
return toListEqualityComparerFunc($x, $y);
}',
            '($x, $y) => {
return toListEqualityComparerFunc($x, $y);
}',
            '$x, $y => \toListEqualityComparerFunc($x, $y)',
            '($x, $y) => \toListEqualityComparerFunc($x, $y)',
            '$x, $y => return \toListEqualityComparerFunc($x, $y);',
            '($x, $y) => return \toListEqualityComparerFunc($x, $y);',
            '$x, $y => { return \toListEqualityComparerFunc($x, $y); }',
            '($x, $y) => { return \toListEqualityComparerFunc($x, $y); }',
            '$x, $y => {
return \toListEqualityComparerFunc($x, $y);
}',
            '($x, $y) => {
return \toListEqualityComparerFunc($x, $y);
}',
        ];
    }

    public function equalityComparerMethod1($x, $y) {
        return toListEqualityComparerFunc($x, $y);
    }

    public static function equalityComparerMethod2($x, $y) {
        return toListEqualityComparerFunc($x, $y);
    }

    public function test1() {
        foreach (static::sequenceListFromArray(['a', 'b', 'c', 'd', 'e']) as $seq) {
            /* @var IEnumerable $seq */

            $list = $seq->toList();

            for ($i = 0; $i < 10; $i++) {
                $this->assertEquals(5, count($list));

                $iterationCount = 0;
                foreach ($list as $index => $item) {
                    ++$iterationCount;

                    $this->assertInternalType('integer', $index);
                    $this->assertTrue(is_int($index));
                    $this->assertTrue(is_integer($index));

                    $this->assertTrue(isset($list[$index]));

                    $listItem = $list[$index];
                    $this->assertSame($item, $listItem);

                    $this->assertInternalType('string', $item);
                    $this->assertTrue(is_string($item));

                    $this->assertSame($item, chr(ord('a') + $index));
                }

                $this->assertEquals($iterationCount, count($list));

                $list->reset();
            }
        }
    }

    public function test2() {
        foreach ($this->createEqualityComparers() as $equalityComparer) {
            foreach (static::sequenceListFromArray(['A', 'b', 'c', 'd', 'e']) as $seq) {
                /* @var IEnumerable $seq */

                $list = $seq->toList($equalityComparer);

                $this->assertEquals(5, count($list));

                $indexB1 = $list->indexOf('B');
                $indexB2 = $list->indexOf('b');
                $this->assertSame(1, $indexB1);
                $this->assertSame(1, $indexB2);
                $this->assertTrue($list->containsItem('b'));
                $this->assertTrue($list->containsItem('B'));

                $indexA1 = $list->indexOf('a');
                $indexA2 = $list->indexOf('A');
                $this->assertSame(0, $indexA1);
                $this->assertSame(0, $indexA2);
                $this->assertTrue($list->containsItem('a'));
                $this->assertTrue($list->containsItem('A'));

                $indexF1 = $list->indexOf('f');
                $indexF2 = $list->indexOf('F');
                $this->assertSame(-1, $indexF1);
                $this->assertSame(-1, $indexF2);
                $this->assertFalse($list->containsItem('F'));
                $this->assertFalse($list->containsItem('f'));
            }
        }
    }
}
