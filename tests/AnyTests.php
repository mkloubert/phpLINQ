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
    return 0 === $x % 2;
}

class PredciateClass {
    public function __invoke($x) {
        return predicateFunc($x);
    }
}

/**
 * @see \System\Collection\IEnumerable::any()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class AnyTests extends TestCaseBase {
    /**
     * Creates the predicates for the tests.
     *
     * @return array The predicates.
     */
    protected function createPredicates() : array {
        return [
            function($x) : bool {
                return predicateFunc($x);
            },
            'predicateFunc',
            '\predicateFunc',
            array($this, 'predciateMethod1'),
            array(static::class, 'predciateMethod2'),
            new PredciateClass(),
            '$x => 0 === $x % 2',
            '($x) => 0 === $x % 2',
            '$x => return 0 === $x % 2;',
            '($x) => return 0 === $x % 2;',
            '$x => { return 0 === $x % 2; }',
            '($x) => { return 0 === $x % 2; }',
            '$x => {
return 0 === $x % 2;
}',
            '($x) => {
return 0 === $x % 2;
}',
        ];
    }

    public function predciateMethod1($x) : bool {
        return predicateFunc($x);
    }

    public static function predciateMethod2($x) : bool {
        return predicateFunc($x);
    }

    public function test1() {
        foreach ($this->createPredicates() as $predicate) {
            foreach (static::sequenceListFromArray([1, 2, 3]) as $seq) {
                /* @var IEnumerable $seq */

                $this->assertTrue($seq->any($predicate));
            }
        }
    }

    public function test2() {
        foreach ($this->createPredicates() as $predicate) {
            foreach (static::sequenceListFromArray([1, 3, 5]) as $seq) {
                /* @var IEnumerable $seq */

                $this->assertFalse($seq->any($predicate));
            }
        }
    }

    public function test3() {
        foreach ($this->createPredicates() as $predicate) {
            foreach (static::sequenceListFromArray([]) as $seq) {
                /* @var IEnumerable $seq */

                $this->assertFalse($seq->any($predicate));
            }
        }
    }

    public function test4() {
        foreach ($this->createPredicates() as $predicate) {
            foreach (static::sequenceListFromArray([1, 3, 5]) as $seq) {
                /* @var IEnumerable $seq */

                $this->assertTrue($seq->any());
            }
        }
    }
}
