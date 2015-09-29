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

use \System\Collections\ElementNotFoundException;
use \System\Collections\IEnumerable;


function firstPredicateFunc($x) : bool {
    return $x > 2;
}

class FirstPredicateClass {
    public function __invoke($x) {
        return firstPredicateFunc($x);
    }
}

/**
 * @see \System\Collections\IEnumerable::first()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class FirstTests extends TestCaseBase {
    /**
     * Creates callable predicates for the tests.
     *
     * @return array The list of callables.
     */
    protected function createPredicates() : array {
        return array(
            function ($x) : bool {
                return firstPredicateFunc($x);
            },
            'firstPredicateFunc',
            '\firstPredicateFunc',
            array($this, 'predicateMethod1'),
            array(static::class, 'predicateMethod2'),
            new FirstPredicateClass(),
            '$x => firstPredicateFunc($x)',
            '($x) => firstPredicateFunc($x)',
            '$x => return firstPredicateFunc($x);',
            '($x) => return firstPredicateFunc($x);',
            '$x => { return firstPredicateFunc($x); }',
            '($x) => { return firstPredicateFunc($x); }',
            '$x => {
return firstPredicateFunc($x);
}',
            '($x) => {
return firstPredicateFunc($x);
}',
            '$x => \firstPredicateFunc($x)',
            '($x) => \firstPredicateFunc($x)',
            '$x => return \firstPredicateFunc($x);',
            '($x) => return \firstPredicateFunc($x);',
            '$x => { return \firstPredicateFunc($x); }',
            '($x) => { return \firstPredicateFunc($x); }',
            '$x => {
return \firstPredicateFunc($x);
}',
            '($x) => {
return \firstPredicateFunc($x);
}',
        );
    }

    public function predicateMethod1($x) {
        return firstPredicateFunc($x);
    }

    public static function predicateMethod2($x) {
        return firstPredicateFunc($x);
    }

    public function testNoPredicate1a() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $item = $seq->first();

            $this->assertEquals(1, $item);
        }
    }

    public function testNoPredicate1b() {
        foreach (static::sequenceListFromArray([]) as $seq) {
            /* @var IEnumerable $seq */

            unset($item);
            unset($thrownEx);

            try {
                $item = $seq->first();
            }
            catch (ElementNotFoundException $ex) {
                $thrownEx = $ex;
            }

            $this->assertFalse(isset($item));
            $this->assertTrue(isset($thrownEx));
            $this->assertInstanceOf(ElementNotFoundException::class, $thrownEx);
        }
    }

    public function testNoPredicate2a() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $item = $seq->first(null);

            $this->assertEquals(1, $item);
        }
    }

    public function testNoPredicate2b() {
        foreach (static::sequenceListFromArray([]) as $seq) {
            /* @var IEnumerable $seq */

            unset($item);
            unset($thrownEx);

            try {
                $item = $seq->first(null);
            }
            catch (ElementNotFoundException $ex) {
                $thrownEx = $ex;
            }

            $this->assertFalse(isset($item));
            $this->assertTrue(isset($thrownEx));
            $this->assertInstanceOf(ElementNotFoundException::class, $thrownEx);
        }
    }

    public function testWithPredicate1() {
        foreach ($this->createPredicates() as $predicate) {
            foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
                /* @var IEnumerable $seq */

                $item = $seq->first($predicate);

                $this->assertEquals(3, $item);
            }
        }
    }

    public function testWithPredicate2() {
        foreach ($this->createPredicates() as $predicate) {
            foreach (static::sequenceListFromArray([1, 2]) as $seq) {
                /* @var IEnumerable $seq */

                unset($item);
                unset($thrownEx);

                try {
                    $item = $seq->first($predicate);
                }
                catch (ElementNotFoundException $ex) {
                    $thrownEx = $ex;
                }

                $this->assertFalse(isset($item));
                $this->assertTrue(isset($thrownEx));
                $this->assertInstanceOf(ElementNotFoundException::class, $thrownEx);
            }
        }
    }
}
