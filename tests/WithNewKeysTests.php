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

use \System\Linq\Enumerable;


function keySelectorFunc($x) : string {
    return trim(strtoupper($x));
}

class KeySelectorClass {
    public function __invoke($x) {
        return keySelectorFunc($x);
    }
}

/**
 * @see \System\Collection\IEnumerable::withNewKeys()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class WithNewKeysTests extends TestCaseBase {
    /**
     * Creates the key selectors for the tests.
     *
     * @return array The key selectors.
     */
    protected function createKeySelectors() : array {
        return [
            function($x) {
                return keySelectorFunc($x);
            },
            'keySelectorFunc',
            '\keySelectorFunc',
            new KeySelectorClass(),
            [$this, 'keySelectorMethod1'],
            [static::class, 'keySelectorMethod2'],
            '$x => keySelectorFunc($x)',
            '$x => \keySelectorFunc($x)',
            '($x) => keySelectorFunc($x)',
            '($x) => \keySelectorFunc($x)',
            '$x => return keySelectorFunc($x);',
            '$x => return \keySelectorFunc($x);',
            '($x) => return keySelectorFunc($x);',
            '($x) => return \keySelectorFunc($x);',
            '$x => { return keySelectorFunc($x); }',
            '$x => { return \keySelectorFunc($x); }',
            '($x) => { return keySelectorFunc($x); }',
            '($x) => { return \keySelectorFunc($x); }',
            '$x => {
return keySelectorFunc($x);
}',
            '$x => {
return \keySelectorFunc($x);
}',
            '($x) => {
return keySelectorFunc($x);
}',
            '($x) => {
return \keySelectorFunc($x);
}',
        ];
    }

    public function keySelectorMethod1($x) {
        return keySelectorFunc($x);
    }

    public static function keySelectorMethod2($x) {
        return keySelectorFunc($x);
    }

    public function test1() {
        foreach ($this->createKeySelectors() as $keySelector) {
            $testData = ['a' => 1, 'B' => 2.0, 'c ' => '3'];

            $sequences   = [];
            $sequences[] = $testData;
            $sequences[] = new ArrayIterator($testData);

            foreach ($sequences as $data) {
                $seq = Enumerable::create($data);

                $items = static::sequenceToArray($seq->withNewKeys($keySelector));

                $this->assertEquals(3, count($items));

                $this->assertTrue(isset($items['A']));
                $this->assertFalse(isset($items['a']));
                $this->assertSame(1, $items['A']);
                $this->assertTrue(isset($items['B']));
                $this->assertSame(2.0, $items['B']);
                $this->assertTrue(isset($items['C']));
                $this->assertFalse(isset($items['c ']));
                $this->assertSame('3', $items['C']);

                $this->assertFalse(isset($items['D']));
                $this->assertFalse(isset($items['d']));
            }
        }
    }
}
