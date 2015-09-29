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
use \System\Collections\EnumerableException;
use \System\Collections\IEnumerable;


function singlePredicateFunc($x) : bool {
    return 0 === $x % 2;
}

class SinglePredicateClass {
    public function __invoke($x) {
        return singlePredicateFunc($x);
    }
}

/**
 * @see \System\Collections\IEnumerable::single()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class SingleTests extends TestCaseBase {
    /**
     * Creates predicates for the tests.
     *
     * @return array The created predicates.
     */
    protected function createPredicates() : array {
        return [
            function($x) {
                return singlePredicateFunc($x);
            },
            array($this, 'predicateMethod1'),
            array(static::class, 'predicateMethod2'),
            new SinglePredicateClass(),
            'singlePredicateFunc',
            '\singlePredicateFunc',
            '$x => singlePredicateFunc($x)',
            '($x) => singlePredicateFunc($x)',
            '$x => return singlePredicateFunc($x);',
            '($x) => return singlePredicateFunc($x);',
            '$x => { return singlePredicateFunc($x); }',
            '($x) => { return singlePredicateFunc($x); }',
            '$x => {
return singlePredicateFunc($x);
}',
            '($x) => {
return singlePredicateFunc($x);
}',
            '$x => \singlePredicateFunc($x)',
            '($x) => \singlePredicateFunc($x)',
            '$x => return \singlePredicateFunc($x);',
            '($x) => return \singlePredicateFunc($x);',
            '$x => { return \singlePredicateFunc($x); }',
            '($x) => { return \singlePredicateFunc($x); }',
            '$x => {
return \singlePredicateFunc($x);
}',
            '($x) => {
return \singlePredicateFunc($x);
}',
        ];
    }

    public function predicateMethod1($x) {
        return singlePredicateFunc($x);
    }

    public static function predicateMethod2($x) {
        return singlePredicateFunc($x);
    }

    public function testWithPredicate() {
        foreach ($this->createPredicates() as $predicate) {
            $seq1 = static::sequenceFromArray([1, 2, 3]);
            $seq2 = static::sequenceFromArray([1, 3, 5]);
            $seq3 = static::sequenceFromArray([]);
            $seq4 = static::sequenceFromArray([22]);

            $item1 = $seq1->single($predicate);

            try {
                $item2 = $seq2->single($predicate);
            }
            catch (ElementNotFoundException $ex) {
                $thrownEx2 = $ex;
            }

            try {
                $item3 = $seq3->single($predicate);
            }
            catch (ElementNotFoundException $ex) {
                $thrownEx3 = $ex;
            }

            $item4 = $seq4->single($predicate);

            $this->assertEquals(2, $item1);

            $this->assertFalse(isset($item2));
            $this->assertTrue(isset($thrownEx2));
            $this->assertInstanceOf(ElementNotFoundException::class, $thrownEx2);

            $this->assertFalse(isset($item3));
            $this->assertTrue(isset($thrownEx3));
            $this->assertInstanceOf(ElementNotFoundException::class, $thrownEx3);

            $this->assertEquals(22, $item4);
        }
    }

    public function testWithPredicateAndException() {
        foreach ($this->createPredicates() as $predicate) {
            $seq1 = static::sequenceFromArray([2, 3, 4]);
            $seq2 = static::sequenceFromArray([6, 8]);

            $exceptionThrown1 = false;
            try {
                $seq1->single($predicate);
            }
            catch (EnumerableException $ex) {
                $exceptionThrown1 = true;
            }

            $exceptionThrown2 = false;
            try {
                $seq2->single($predicate);
            }
            catch (EnumerableException $ex) {
                $exceptionThrown2 = true;
            }

            $this->assertTrue($exceptionThrown1);
            $this->assertTrue($exceptionThrown2);
        }
    }

    public function testWithoutPredicate() {
        $seq1 = static::sequenceFromArray([1]);
        $seq2 = static::sequenceFromArray([]);

        $item1 = $seq1->single();

        try {
            $item2 = $seq2->single();
        }
        catch (ElementNotFoundException $ex) {
            $thrownEx2 = $ex;
        }

        $this->assertEquals(1, $item1);

        $this->assertFalse(isset($item2));
        $this->assertTrue(isset($thrownEx2));
        $this->assertInstanceOf(ElementNotFoundException::class, $thrownEx2);
    }

    public function testWithoutPredicateAndWithException() {
        foreach (static::sequenceListFromArray([1, 2]) as $seq) {
            /* @var IEnumerable $seq */

            $exceptionThrown = false;
            try {
                $seq->single();
            }
            catch (EnumerableException $ex) {
                $exceptionThrown = true;
            }

            $this->assertTrue($exceptionThrown);
        }
    }
}
