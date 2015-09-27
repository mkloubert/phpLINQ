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


function predicateFunc($x) : bool {
    return $x > 2;
}

class PredicateClass {
    public function __invoke($x) {
        return predicateFunc($x);
    }
}

/**
 * @see \System\Collection\IEnumerable::firstOrDefault()
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
            new PredicateClass(),
            '$x => predicateFunc($x)',
            '($x) => predicateFunc($x)',
            '$x => return predicateFunc($x);',
            '($x) => return predicateFunc($x);',
            '$x => { return predicateFunc($x); }',
            '($x) => { return predicateFunc($x); }',
            '$x => {
return predicateFunc($x);
}',
            '($x) => {
return predicateFunc($x);
}',
            '$x => \predicateFunc($x)',
            '($x) => \predicateFunc($x)',
            '$x => return \predicateFunc($x);',
            '($x) => return \predicateFunc($x);',
            '$x => { return \predicateFunc($x); }',
            '($x) => { return \predicateFunc($x); }',
            '$x => {
return \predicateFunc($x);
}',
            '($x) => {
return \predicateFunc($x);
}',
        );
    }

    public function predicateMethod1($x) {
        return predicateFunc($x);
    }

    public static function predicateMethod2($x) {
        return predicateFunc($x);
    }

    public function testNoPredicate1a() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $item = $seq->firstOrDefault();

            $this->assertNotEquals(null, $item);
            $this->assertEquals(1, $item);
        }
    }

    public function testNoPredicate1b() {
        foreach (static::sequenceListFromArray([]) as $seq) {
            /* @var IEnumerable $seq */

            $item = $seq->firstOrDefault();

            $this->assertEquals(null, $item);
        }
    }

    public function testNoPredicate2a() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $item = $seq->firstOrDefault(null, 'How I Met Your Mother');

            $this->assertNotEquals('How I Met Your Mother', $item);
            $this->assertEquals(1, $item);
        }
    }

    public function testNoPredicate2b() {
        foreach (static::sequenceListFromArray([]) as $seq) {
            /* @var IEnumerable $seq */

            $item = $seq->firstOrDefault(null, 'Fear The Walking Dead');

            $this->assertEquals('Fear The Walking Dead', $item);
        }
    }

    public function testNoPredicateWithDefault1() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $item = $seq->firstOrDefault(false);

            $this->assertNotEquals(false, $item);
            $this->assertEquals(1, $item);
        }
    }

    public function testNoPredicateWithDefault2() {
        foreach (static::sequenceListFromArray([]) as $seq) {
            /* @var IEnumerable $seq */

            $item = $seq->firstOrDefault(false);

            $this->assertEquals(false, $item);
        }
    }

    public function testWithPredicate1() {
        foreach ($this->createPredicates() as $predicate) {
            foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
                /* @var IEnumerable $seq */

                $item = $seq->firstOrDefault($predicate);

                $this->assertNotEquals(null, $item);
                $this->assertEquals(3, $item);
            }
        }
    }

    public function testWithPredicate2() {
        foreach ($this->createPredicates() as $predicate) {
            foreach (static::sequenceListFromArray([1, 2]) as $seq) {
                /* @var IEnumerable $seq */

                $item = $seq->firstOrDefault($predicate);

                $this->assertEquals(null, $item);
            }
        }
    }

    public function testWithPredicate3() {
        foreach ($this->createPredicates() as $predicate) {
            foreach (static::sequenceListFromArray([2, -1, 1]) as $seq) {
                /* @var IEnumerable $seq */

                $item = $seq->firstOrDefault($predicate);

                $this->assertEquals(null, $item);
            }
        }
    }

    public function testWithPredicate4() {
        foreach ($this->createPredicates() as $predicate) {
            foreach (static::sequenceListFromArray([]) as $seq) {
                /* @var IEnumerable $seq */

                $item = $seq->firstOrDefault($predicate);

                $this->assertEquals(null, $item);
            }
        }
    }
}
