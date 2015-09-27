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


function sequenceEqualEqualityComparerFunc($x, $y) : bool {
    return $x === $y;
}

class SequenceEqualEqualityComparerClass {
    public function __invoke($x, $y) {
        return sequenceEqualEqualityComparerFunc($x, $y);
    }
}

/**
 * @see \System\Collection\IEnumerable::sequenceEqual()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class SequenceEqualTests extends TestCaseBase {
    /**
     * Creates equality comparers for the tests.
     *
     * @return array The equality comparers.
     */
    protected function createEqualityComparers() : array {
        return [
            true,
            function($x, $y) {
                return sequenceEqualEqualityComparerFunc($x, $y);
            },
            'sequenceEqualEqualityComparerFunc',
            '\sequenceEqualEqualityComparerFunc',
            array($this, 'equalityComparerMethod1'),
            array(static::class, 'equalityComparerMethod2'),
            new SequenceEqualEqualityComparerClass(),
            '$x, $y => sequenceEqualEqualityComparerFunc($x, $y)',
            '($x, $y) => sequenceEqualEqualityComparerFunc($x, $y)',
            '$x, $y => return sequenceEqualEqualityComparerFunc($x, $y);',
            '($x, $y) => return sequenceEqualEqualityComparerFunc($x, $y);',
            '$x, $y => { return sequenceEqualEqualityComparerFunc($x, $y); }',
            '($x, $y) => { return sequenceEqualEqualityComparerFunc($x, $y); }',
            '$x, $y => {
return sequenceEqualEqualityComparerFunc($x, $y);
}',
            '($x, $y) => {
return sequenceEqualEqualityComparerFunc($x, $y);
}',
            '$x, $y => \sequenceEqualEqualityComparerFunc($x, $y)',
            '($x, $y) => \sequenceEqualEqualityComparerFunc($x, $y)',
            '$x, $y => return \sequenceEqualEqualityComparerFunc($x, $y);',
            '($x, $y) => return \sequenceEqualEqualityComparerFunc($x, $y);',
            '$x, $y => { return \sequenceEqualEqualityComparerFunc($x, $y); }',
            '($x, $y) => { return \sequenceEqualEqualityComparerFunc($x, $y); }',
            '$x, $y => {
return \sequenceEqualEqualityComparerFunc($x, $y);
}',
            '($x, $y) => {
return \sequenceEqualEqualityComparerFunc($x, $y);
}',
        ];
    }

    public function equalityComparerMethod1($x, $y) : bool {
        return sequenceEqualEqualityComparerFunc($x, $y);
    }

    public static function equalityComparerMethod2($x, $y) : bool {
        return sequenceEqualEqualityComparerFunc($x, $y);
    }

    public function testArray1a() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $other = [1, 2, 3, 4, 5];

            $this->assertTrue($seq->sequenceEqual($other));
        }
    }

    public function testArray1b() {
        foreach ($this->createEqualityComparers() as $equalityComparer) {
            foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
                /* @var IEnumerable $seq */

                $other = [1, 2, 3, 4, 5];

                $this->assertTrue($seq->sequenceEqual($other, $equalityComparer));
            }
        }
    }

    public function testArray2a() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $other = [1, 2.0, 3, '4', 5];

            $this->assertTrue($seq->sequenceEqual($other));
        }
    }

    public function testArray2b() {
        foreach ($this->createEqualityComparers() as $equalityComparer) {
            foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
                /* @var IEnumerable $seq */

                $other = [1, 2.0, 3, '4', 5];

                $this->assertFalse($seq->sequenceEqual($other, $equalityComparer));
            }
        }
    }

    public function testArray3() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $other = [1, 2, 3, 4];

            $this->assertFalse($seq->sequenceEqual($other));
        }
    }

    public function testArray4() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $other = [1, 2.0, 3, '4'];

            $this->assertFalse($seq->sequenceEqual($other));
        }
    }

    public function testArray5() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4]) as $seq) {
            /* @var IEnumerable $seq */

            $other = [1, 2, 3, 4, 5];

            $this->assertFalse($seq->sequenceEqual($other));
        }
    }

    public function testArray6() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4]) as $seq) {
            /* @var IEnumerable $seq */

            $other = [1, 2.0, 3, '4', 5];

            $this->assertFalse($seq->sequenceEqual($other));
        }
    }

    public function testGenerator1() {
        $createGenerator = function() {
            yield 1;
            yield 2;
            yield 3;
            yield 4;
            yield 5;
        };

        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $other = $createGenerator();

            $this->assertTrue($seq->sequenceEqual($other));
        }
    }

    public function testGenerator2a() {
        $createGenerator = function() {
            yield 1;
            yield 2.0;
            yield 3;
            yield '4';
            yield 5;
        };

        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $other = $createGenerator();

            $this->assertTrue($seq->sequenceEqual($other));
        }
    }

    public function testGenerator2b() {
        $createGenerator = function() {
            yield 1;
            yield 2.0;
            yield 3;
            yield '4';
            yield 5;
        };

        foreach ($this->createEqualityComparers() as $equalityComparer) {
            foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
                /* @var IEnumerable $seq */

                $other = $createGenerator();

                $this->assertFalse($seq->sequenceEqual($other, $equalityComparer));
            }
        }
    }

    public function testGenerator3() {
        $createGenerator = function() {
            yield 1;
            yield 2;
            yield 3;
            yield 4;
        };

        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $other = $createGenerator();

            $this->assertFalse($seq->sequenceEqual($other));
        }
    }

    public function testGenerator4() {
        $createGenerator = function() {
            yield 1;
            yield 2.0;
            yield 3;
            yield '4';
        };

        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $other = $createGenerator();

            $this->assertFalse($seq->sequenceEqual($other));
        }
    }

    public function testGenerator5() {
        $createGenerator = function() {
            yield 1;
            yield 2;
            yield 3;
            yield 4;
            yield 5;
        };

        foreach (static::sequenceListFromArray([1, 2, 3, 4]) as $seq) {
            /* @var IEnumerable $seq */

            $other = $createGenerator();

            $this->assertFalse($seq->sequenceEqual($other));
        }
    }

    public function testGenerator6() {
        $createGenerator = function() {
            yield 1;
            yield 2.0;
            yield 3;
            yield '4';
            yield 5;
        };

        foreach (static::sequenceListFromArray([1, 2, 3, 4]) as $seq) {
            /* @var IEnumerable $seq */

            $other = $createGenerator();

            $this->assertFalse($seq->sequenceEqual($other));
        }
    }

    public function testIterator1() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $other = new ArrayIterator([1, 2, 3, 4, 5]);

            $this->assertTrue($seq->sequenceEqual($other));
        }
    }

    public function testIterator2a() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $other = new ArrayIterator([1, 2.0, 3, '4', 5]);

            $this->assertTrue($seq->sequenceEqual($other));
        }
    }

    public function testIterator2b() {
        foreach ($this->createEqualityComparers() as $equalityComparer) {
            foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
                /* @var IEnumerable $seq */

                $other = new ArrayIterator([1, 2.0, 3, '4', 5]);

                $this->assertFalse($seq->sequenceEqual($other, $equalityComparer));
            }
        }
    }

    public function testIterator3() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $other = new ArrayIterator([1, 2, 3, 4]);

            $this->assertFalse($seq->sequenceEqual($other));
        }
    }

    public function testIterator4() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $other = new ArrayIterator([1, 2.0, 3, '4']);

            $this->assertFalse($seq->sequenceEqual($other));
        }
    }

    public function testIterator5() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4]) as $seq) {
            /* @var IEnumerable $seq */

            $other = new ArrayIterator([1, 2, 3, 4, 5]);

            $this->assertFalse($seq->sequenceEqual($other));
        }
    }

    public function testIterator6() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4]) as $seq) {
            /* @var IEnumerable $seq */

            $other = new ArrayIterator([1, 2.0, 3, '4', 5]);

            $this->assertFalse($seq->sequenceEqual($other));
        }
    }

    public function testString1() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $other = '12345';

            $this->assertTrue($seq->sequenceEqual($other));
        }
    }

    public function testString2() {
        foreach ($this->createEqualityComparers() as $equalityComparer) {
            foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
                /* @var IEnumerable $seq */

                $other = '12345';

                $this->assertFalse($seq->sequenceEqual($other, $equalityComparer));
            }
        }
    }

    public function testString3() {
        foreach (static::sequenceListFromArray([1, '2', 3, 4.0, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $other = '12345';

            $this->assertTrue($seq->sequenceEqual($other));
        }
    }

    public function testString4() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $other = '1234';

            $this->assertFalse($seq->sequenceEqual($other));
        }
    }

    public function testString5() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4]) as $seq) {
            /* @var IEnumerable $seq */

            $other = '12345';

            $this->assertFalse($seq->sequenceEqual($other));
        }
    }

    public function testString6() {
        foreach (static::sequenceListFromArray([1, 2.0, 3, '4', 5]) as $seq) {
            /* @var IEnumerable $seq */

            $other = '1234';

            $this->assertFalse($seq->sequenceEqual($other));
        }
    }

    public function testString7() {
        foreach (static::sequenceListFromArray([1, 2.0, 3, '4']) as $seq) {
            /* @var IEnumerable $seq */

            $other = '12345';

            $this->assertFalse($seq->sequenceEqual($other));
        }
    }
}
