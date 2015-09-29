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


function stringConcatAccumulatorFunc($result, $x) : string {
    return $result . $x;
}

class StringConcatAccumulatorClass {
    public function __invoke($result, $x) {
        return stringConcatAccumulatorFunc($result, $x);
    }
}

/**
 * @see \System\Collections\IEnumerable::aggregate()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class AggregateTests extends TestCaseBase {
    /**
     * Creates accumulators for AggregateTests::testStringConcat() method.
     *
     * @return array The list of accumulators.
     */
    protected function createStringConcatAccumulator() : array {
        return [
            function($result, $x) {
                return stringConcatAccumulatorFunc($result, $x);
            },
            'stringConcatAccumulatorFunc',
            '\stringConcatAccumulatorFunc',
            array($this, 'stringConcatAccumulatorMethod1'),
            array(static::class, 'stringConcatAccumulatorMethod2'),
            new StringConcatAccumulatorClass(),
            '$result, $x => stringConcatAccumulatorFunc($result, $x)',
            '($result, $x) => stringConcatAccumulatorFunc($result, $x)',
            '$result, $x => return stringConcatAccumulatorFunc($result, $x);',
            '($result, $x) => return stringConcatAccumulatorFunc($result, $x);',
            '$result, $x => { return stringConcatAccumulatorFunc($result, $x); }',
            '($result, $x) => { return stringConcatAccumulatorFunc($result, $x); }',
            '$result, $x => {
return stringConcatAccumulatorFunc($result, $x);
}',
            '($result, $x) => {
return stringConcatAccumulatorFunc($result, $x);
}',
            '$result, $x => \stringConcatAccumulatorFunc($result, $x)',
            '($result, $x) => \stringConcatAccumulatorFunc($result, $x)',
            '$result, $x => return \stringConcatAccumulatorFunc($result, $x);',
            '($result, $x) => return \stringConcatAccumulatorFunc($result, $x);',
            '$result, $x => { return \stringConcatAccumulatorFunc($result, $x); }',
            '($result, $x) => { return \stringConcatAccumulatorFunc($result, $x); }',
            '$result, $x => {
return \stringConcatAccumulatorFunc($result, $x);
}',
            '($result, $x) => {
return \stringConcatAccumulatorFunc($result, $x);
}',
        ];
    }

    public function stringConcatAccumulatorMethod1($result, $x) {
        return stringConcatAccumulatorFunc($result, $x);
    }

    public static function stringConcatAccumulatorMethod2($result, $x) {
        return stringConcatAccumulatorFunc($result, $x);
    }

    public function testStringConcat1() {
        foreach ($this->createStringConcatAccumulator() as $accumulator) {
            foreach (static::sequenceListFromArray(['A', 'BB', 'c']) as $seq) {
                /* @var IEnumerable $seq */

                $val = $seq->aggregate($accumulator, 666);

                $this->assertNotEquals(666, $val);
                $this->assertEquals('ABBc', $val);
            }
        }
    }

    public function testStringConcat2() {
        foreach ($this->createStringConcatAccumulator() as $accumulator) {
            foreach (static::sequenceListFromArray([]) as $seq) {
                /* @var IEnumerable $seq */

                $val = $seq->aggregate($accumulator, 666);

                $this->assertEquals(666, $val);
            }
        }
    }
}
