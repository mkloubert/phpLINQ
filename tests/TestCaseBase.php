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
use \System\Linq\Enumerable;


/**
 * A basic test case.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
abstract class TestCaseBase extends PHPUnit_Framework_TestCase {
    /**
     * Checks a sequence if it has an expected list of same values (in the same order).
     *
     * @param IEnumerable $seq The sequence.
     * @param array $expected The expected values.
     * @param bool $exact Check values exactly or not.
     */
    protected function checkForExpectedValues(IEnumerable $seq, array $expected = array(), bool $exact = true) {
        foreach ($expected as $index => $ev) {
            $seq->reset();

            $count = $index;
            while ($count-- > 0 && $seq->valid()) {
                $seq->next();
            }

            $av = $seq->current();
            if ($exact) {
                $this->assertSame($ev, $av);
            }
            else {
                $this->assertEquals($ev, $av);
            }
        }
    }

    /**
     * @param array $arr
     * @return Generator
     */
    protected static function generatorFromArray(array $arr = []) : Generator {
        foreach ($arr as $key => $value) {
            yield $key => $value;
        }
    }

    /**
     * Creates a new sequence from an array.
     *
     * @param array $arr The array with the data for the sequence.
     *
     * @return Enumerable The created sequence.
     */
    protected static function sequenceFromArray(array $arr = []) : Enumerable {
        return Enumerable::create($arr);
    }

    /**
     * Creates a list of sequences from an array.
     *
     * @param array $arr The array with the data for each sequence.
     *
     * @return IEnumerable[] The list of sequences.
     */
    protected static function sequenceListFromArray(array $arr = []) : array {
        return [
            static::sequenceFromArray($arr),
            Enumerable::create(new \ArrayIterator($arr)),
            Enumerable::create(static::generatorFromArray($arr)),
        ];
    }

    /**
     * Creates an array from a sequence.
     *
     * @param IEnumerable $seq The sequence.
     * @param bool $preventKeys Prevent keys or not.
     *
     * @return array The sequence as array.
     */
    protected static function sequenceToArray(IEnumerable $seq, bool $preventKeys = true) : array {
        $result = [];
        foreach ($seq as $key => $value) {
            if ($preventKeys) {
                $result[$key] = $value;
            }
            else {
                $result[] = $value;
            }
        }

        return $result;
    }
}
