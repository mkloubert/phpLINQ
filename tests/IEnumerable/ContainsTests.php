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


function containsEqualityComparerFunc($x, $y) : bool {
    return $x === $y;
}

class ContainsEqualityComparerClass {
    public function __invoke($x, $y) {
        return containsEqualityComparerFunc($x, $y);
    }
}

/**
 * @see \System\Collections\IEnumerable::contains()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class ContainsTests extends TestCaseBase {
    /**
     * Creates the equality comparers for the tests.
     *
     * @return array The equality comparers.
     */
    protected function createEqualityComparers() : array {
        return [
            true,
            function($x, $y) : bool {
                return containsEqualityComparerFunc($x, $y);
            },
            'containsEqualityComparerFunc',
            '\containsEqualityComparerFunc',
            array($this, 'equalityComparerMethod1'),
            array(static::class, 'equalityComparerMethod2'),
            new ContainsEqualityComparerClass(),
            '$x, $y => containsEqualityComparerFunc($x, $y)',
            '($x, $y) => containsEqualityComparerFunc($x, $y)',
            '$x, $y => return containsEqualityComparerFunc($x, $y);',
            '($x, $y) => return containsEqualityComparerFunc($x, $y);',
            '$x, $y => { return containsEqualityComparerFunc($x, $y); }',
            '($x, $y) => { return containsEqualityComparerFunc($x, $y); }',
            '$x, $y => {
return containsEqualityComparerFunc($x, $y);
}',
            '($x, $y) => {
return containsEqualityComparerFunc($x, $y);
}',
            '$x, $y => \containsEqualityComparerFunc($x, $y)',
            '($x, $y) => \containsEqualityComparerFunc($x, $y)',
            '$x, $y => return \containsEqualityComparerFunc($x, $y);',
            '($x, $y) => return \containsEqualityComparerFunc($x, $y);',
            '$x, $y => { return \containsEqualityComparerFunc($x, $y); }',
            '($x, $y) => { return \containsEqualityComparerFunc($x, $y); }',
            '$x, $y => {
return \containsEqualityComparerFunc($x, $y);
}',
            '($x, $y) => {
return \containsEqualityComparerFunc($x, $y);
}',
        ];
    }

    public function equalityComparerMethod1($x, $y) : bool {
        return containsEqualityComparerFunc($x, $y);
    }

    public static function equalityComparerMethod2($x, $y) : bool {
        return containsEqualityComparerFunc($x, $y);
    }

    public function test1a() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $this->assertTrue($seq->contains(2));
        }
    }

    public function test1b() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $this->assertTrue($seq->contains(2.0));
        }
    }

    public function test1c() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $this->assertTrue($seq->contains('2'));
        }
    }

    public function test2a() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $this->assertFalse($seq->contains(6));
        }
    }

    public function test2b() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $this->assertFalse($seq->contains(6.0));
        }
    }

    public function test2c() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $this->assertFalse($seq->contains('6'));
        }
    }

    public function test3a() {
        foreach (static::sequenceListFromArray([]) as $seq) {
            /* @var IEnumerable $seq */

            $this->assertFalse($seq->contains(5));
        }
    }

    public function test3b() {
        foreach (static::sequenceListFromArray([]) as $seq) {
            /* @var IEnumerable $seq */

            $this->assertFalse($seq->contains(5.0));
        }
    }

    public function test3c() {
        foreach (static::sequenceListFromArray([]) as $seq) {
            /* @var IEnumerable $seq */

            $this->assertFalse($seq->contains('5'));
        }
    }

    public function test4a() {
        foreach ($this->createEqualityComparers() as $equalityComparer) {
            foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
                /* @var IEnumerable $seq */

                $this->assertTrue($seq->contains(1, $equalityComparer));
            }
        }
    }

    public function test4b() {
        foreach ($this->createEqualityComparers() as $equalityComparer) {
            foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
                /* @var IEnumerable $seq */

                $this->assertFalse($seq->contains(1.0, $equalityComparer));
            }
        }
    }

    public function test4c() {
        foreach ($this->createEqualityComparers() as $equalityComparer) {
            foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
                /* @var IEnumerable $seq */

                $this->assertFalse($seq->contains('1', $equalityComparer));
            }
        }
    }
}
