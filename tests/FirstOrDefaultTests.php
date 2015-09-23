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


function predicateFunc($x) : bool {
    return $x > 2;
}

/**
 * @see \System\Collection\IEnumerable::firstOrDefault().
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class FirstOrDefaultTests extends TestCaseBase {
    /**
     * Creates callable predicates for the tests.
     *
     * @return array The list of callables.
     */
    protected function createPredicates() : array {
        return array(
            function ($x) : bool {
                return predicateFunc($x);
            },
            'predicateFunc',
            '\predicateFunc',
            array($this, 'predicateMethod1'),
            array(static::class, 'predicateMethod2'),
            '$x => $x > 2',
            '($x) => $x > 2',
            '$x => return $x > 2;',
            '($x) => return $x > 2;',
            '$x => { return $x > 2; }',
            '($x) => { return $x > 2; }',
            '$x => {
return $x > 2;
}',
            '($x) => {
return $x > 2;
}',
        );
    }

    public function predicateMethod1($x) {
        return predicateFunc($x);
    }

    public static function predicateMethod2($x) {
        return predicateFunc($x);
    }

    public function testNoPredicate1() {
        $seq1 = static::sequenceFromArray([1, 2, 3, 4, 5]);
        $seq2 = static::sequenceFromArray([]);

        $item1 = $seq1->firstOrDefault();
        $item2 = $seq2->firstOrDefault();

        $this->assertEquals(1, $item1);
        $this->assertEquals(null, $item2);
    }

    public function testNoPredicate2() {
        $seq1 = static::sequenceFromArray([1, 2, 3, 4, 5]);
        $seq2 = static::sequenceFromArray([]);

        $item1 = $seq1->firstOrDefault(null, 'xyz');
        $item2 = $seq2->firstOrDefault(null, 'xyz');

        $this->assertEquals(1, $item1);
        $this->assertEquals('xyz', $item2);
    }

    public function testNoPredicateWithDefault() {
        $seq1 = static::sequenceFromArray([1, 2, 3, 4, 5]);
        $seq2 = static::sequenceFromArray([]);

        $item1 = $seq1->firstOrDefault(false);
        $item2 = $seq2->firstOrDefault(false);

        $this->assertEquals(1, $item1);
        $this->assertEquals(false, $item2);
    }

    public function testWithPredicate() {
        foreach ($this->createPredicates() as $predicate) {
            $seq1 = static::sequenceFromArray([1, 2, 3, 4, 5]);
            $seq2 = static::sequenceFromArray([1, 2]);
            $seq3 = static::sequenceFromArray([2, -1, 1]);
            $seq4 = static::sequenceFromArray([]);

            $item1 = $seq1->firstOrDefault($predicate);
            $item2 = $seq2->firstOrDefault($predicate);
            $item3 = $seq3->firstOrDefault($predicate);
            $item4 = $seq4->firstOrDefault($predicate);

            $this->assertEquals(3, $item1);
            $this->assertEquals(null, $item2);
            $this->assertEquals(null, $item3);
            $this->assertEquals(null, $item4);
        }
    }
}
