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


function toSetEqualityComparerFunc($x, $y): bool {
    return $x === $y;
}

class ToSetEqualityComparerClass {
    public function __invoke($x, $y) {
        return toSetEqualityComparerFunc($x, $y);
    }
}

/**
 * @see \System\Collection\IEnumerable::toSet()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class ToSetTests extends TestCaseBase {
    /**
     * Creates the equality comparers for the tests.
     *
     * @return array The equality comparers.
     */
    protected function createEqualityComparers() : array {
        return [
            function($x, $y) {
                return toSetEqualityComparerFunc($x, $y);
            },
            'toSetEqualityComparerFunc',
            '\toSetEqualityComparerFunc',
            new ToSetEqualityComparerClass(),
            array($this, 'equalityComparerMethod1'),
            array(static::class, 'equalityComparerMethod2'),
            '$x, $y => toSetEqualityComparerFunc($x, $y)',
            '($x, $y) => toSetEqualityComparerFunc($x, $y)',
            '$x, $y => return toSetEqualityComparerFunc($x, $y);',
            '($x, $y) => return toSetEqualityComparerFunc($x, $y);',
            '$x, $y => { return toSetEqualityComparerFunc($x, $y); }',
            '($x, $y) => { return toSetEqualityComparerFunc($x, $y); }',
            '$x, $y => {
return toSetEqualityComparerFunc($x, $y);
}',
            '($x, $y) => {
return toSetEqualityComparerFunc($x, $y);
}',
            '$x, $y => \toSetEqualityComparerFunc($x, $y)',
            '($x, $y) => \toSetEqualityComparerFunc($x, $y)',
            '$x, $y => return \toSetEqualityComparerFunc($x, $y);',
            '($x, $y) => return \toSetEqualityComparerFunc($x, $y);',
            '$x, $y => { return \toSetEqualityComparerFunc($x, $y); }',
            '($x, $y) => { return \toSetEqualityComparerFunc($x, $y); }',
            '$x, $y => {
return \toSetEqualityComparerFunc($x, $y);
}',
            '($x, $y) => {
return \toSetEqualityComparerFunc($x, $y);
}',
        ];
    }

    public function equalityComparerMethod1($x, $y) {
        return toSetEqualityComparerFunc($x, $y);
    }

    public static function equalityComparerMethod2($x, $y) {
        return toSetEqualityComparerFunc($x, $y);
    }

    public function test1() {
        foreach (static::sequenceListFromArray([1, 2, '3', '2', 3.0]) as $seq) {
            /* @var IEnumerable $seq */

            $s = $seq->toSet();

            $this->assertEquals(3, count($s));

            $expected = [1, 2, '3'];
            foreach ($expected as $index => $e) {
                $s->reset();

                $count = $index;
                while ($count-- > 0 && $s->valid()) {
                    $s->next();
                }

                $this->assertSame($e, $s->current());
            }
        }
    }

    public function test2() {
        foreach ($this->createEqualityComparers() as $equalityComparer) {
            foreach (static::sequenceListFromArray([1, 2, '3', '2', '3']) as $seq) {
                /* @var IEnumerable $seq */

                $s = $seq->toSet($equalityComparer);

                $this->assertEquals(4, count($s));

                $expected = [1, 2, '3', '2'];
                foreach ($expected as $index => $e) {
                    $s->reset();

                    $count = $index;
                    while ($count-- > 0 && $s->valid()) {
                        $s->next();
                    }

                    $this->assertSame($e, $s->current());
                }
            }
        }
    }
}
