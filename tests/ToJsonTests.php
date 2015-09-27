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


function keySelectorFunc($key) : string {
    return chr(ord('A') + $key);
}

class KeySelectorClass {
    public function __invoke($key) {
        return keySelectorFunc($key);
    }
}

/**
 * @see \System\Collection\IEnumerable::toJson()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class ToJsonTests extends TestCaseBase {
    /**
     * Creates key selectors for the tests.
     *
     * @return array The key selectors.
     */
    protected function createKeySelectors() : array {
        return [
            function ($key) {
                return keySelectorFunc($key);
            },
            'keySelectorFunc',
            '\keySelectorFunc',
            array($this, 'keySelectorMethod1'),
            array(static::class, 'keySelectorMethod2'),
            new KeySelectorClass(),
            '$key => keySelectorFunc($key)',
            '($key) => keySelectorFunc($key)',
            '$key => return keySelectorFunc($key);',
            '($key) => return keySelectorFunc($key);',
            '$key => { return keySelectorFunc($key); }',
            '($key) => { return keySelectorFunc($key); }',
            '$key => {
return keySelectorFunc($key);
}',
            '($key) => {
return keySelectorFunc($key);
}',
            '$key => \keySelectorFunc($key)',
            '($key) => \keySelectorFunc($key)',
            '$key => return \keySelectorFunc($key);',
            '($key) => return \keySelectorFunc($key);',
            '$key => { return \keySelectorFunc($key); }',
            '($key) => { return \keySelectorFunc($key); }',
            '$key => {
return \keySelectorFunc($key);
}',
            '($key) => {
return \keySelectorFunc($key);
}',
        ];
    }

    public function keySelectorMethod1($x) {
        return keySelectorFunc($x);
    }

    public static function keySelectorMethod2($x) {
        return keySelectorFunc($x);
    }

    /**
     * @see \System\Collection\IEnumerable::toJson()
     *
     * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
     */
    public function test1() {
        foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
            /* @var IEnumerable $seq */

            $json = $seq->toJson();
            $arr  = \json_decode($json, true);

            $this->assertEquals(5, count($arr));

            foreach ($arr as $key => $value) {
                $this->assertTrue('integer' === gettype($value));
                $this->assertTrue(is_int($value));
                $this->assertTrue(is_integer($value));
                $this->assertTrue(isset($arr[$value - 1]));
                $this->assertEquals($key, $value - 1);
            }
        }
    }

    public function test2() {
        $seq = static::sequenceFromArray(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5]);

        $json = $seq->toJson();
        $arr  = \json_decode($json, true);

        $this->assertEquals(5, count($arr));

        foreach ($arr as $key => $value) {
            $this->assertTrue('string' === gettype($key));
            $this->assertTrue(is_string($key));
            $this->assertEquals($key, chr(ord('a') + $value - 1));

            $this->assertTrue('integer' === gettype($value));
            $this->assertTrue(is_int($value));
            $this->assertTrue(is_integer($value));
        }
    }

    public function testKeySelector() {
        foreach ($this->createKeySelectors() as $selector) {
            foreach (static::sequenceListFromArray([1, 2, 3, 4, 5]) as $seq) {
                /* @var IEnumerable $seq */

                $json = $seq->toJson($selector);
                $arr  = \json_decode($json, true);

                $this->assertEquals(5, count($arr));

                foreach ($arr as $key => $value) {
                    $this->assertTrue('string' === gettype($key));
                    $this->assertTrue(is_string($key));
                    $this->assertEquals($key, chr(ord('A') + $value - 1));

                    $this->assertTrue('integer' === gettype($value));
                    $this->assertTrue(is_int($value));
                    $this->assertTrue(is_integer($value));
                }
            }
        }
    }
}
