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
    return $x < 4;
}

class PredicateClass {
    public function __invoke($x) {
        return predicateFunc($x);
    }
}

/**
 * @see \System\Collection\IEnumerable::lastOrDefault()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class LastOrDefaultTests extends TestCaseBase {
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
            new PredicateClass(),
            '$x => $x < 4',
            '($x) => $x < 4',
            '$x => return $x < 4;',
            '($x) => return $x < 4;',
            '$x => { return $x < 4; }',
            '($x) => { return $x < 4; }',
            '$x => {
return $x < 4;
}',
            '($x) => {
return $x < 4;
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

        $item1 = $seq1->lastOrDefault();
        $item2 = $seq2->lastOrDefault();

        $this->assertEquals(5, $item1);
        $this->assertEquals(null, $item2);
    }

    public function testNoPredicate2() {
        $seq1 = static::sequenceFromArray([1, 2, 3, 4, 5]);
        $seq2 = static::sequenceFromArray([]);

        $item1 = $seq1->lastOrDefault(null, 'abc');
        $item2 = $seq2->lastOrDefault(null, 'abc');

        $this->assertEquals(5, $item1);
        $this->assertEquals('abc', $item2);
    }

    public function testNoPredicateWithDefault() {
        $seq1 = static::sequenceFromArray([1, 2, 3, 4, 5]);
        $seq2 = static::sequenceFromArray([]);

        $item1 = $seq1->lastOrDefault(false);
        $item2 = $seq2->lastOrDefault(false);

        $this->assertEquals(5, $item1);
        $this->assertEquals(false, $item2);
    }

    public function testWithPredicate() {
        foreach ($this->createPredicates() as $predicate) {
            $seq1 = static::sequenceFromArray([1, 2, 3, 4, 5]);
            $seq2 = static::sequenceFromArray([6, 5, 4]);
            $seq3 = static::sequenceFromArray([]);

            $item1 = $seq1->lastOrDefault($predicate);
            $item2 = $seq2->lastOrDefault($predicate);
            $item3 = $seq3->lastOrDefault($predicate);

            $this->assertEquals(3, $item1);
            $this->assertEquals(null, $item2);
            $this->assertEquals(null, $item3);
        }
    }
}
