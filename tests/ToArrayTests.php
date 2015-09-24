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


function keySelectorFunc($key, $item) {
    return sprintf('%s%s', strtoupper($key), $item + 1);
}

class KeySelectorClass {
    public function __invoke($key, $item) {
        return keySelectorFunc($key, $item);
    }
}

/**
 * @see \System\Collection\IEnumerable::toArray()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class ToArrayTests extends TestCaseBase {
    /**
     * Creates a list of key selectors.
     *
     * @return array The selectors.
     */
    protected function createKeySelectors() : array {
        return array(
            function($key, $item) {
                return keySelectorFunc($key, $item);
            },
            array($this, 'keySelectorMethod1'),
            array(static::class, 'keySelectorMethod2'),
            new KeySelectorClass(),
            'keySelectorFunc',
            '$key, $item => sprintf("%s%s", strtoupper($key), $item + 1)',
            '($key, $item) => sprintf("%s%s", strtoupper($key), $item + 1)',
            '$key, $item => return sprintf("%s%s", strtoupper($key), $item + 1); ',
            '($key, $item) =>  return  sprintf("%s%s", strtoupper($key), $item + 1);',
            '$key, $item => { return sprintf("%s%s", strtoupper($key), $item + 1); }',
            '($key, $item) => { return  sprintf("%s%s", strtoupper($key), $item + 1);  }',
            '$key, $item => {
return sprintf("%s%s", strtoupper($key), $item + 1);
}',
            '($key, $item) =>  {
return  sprintf("%s%s", strtoupper($key), $item + 1);
}',
            '\keySelectorFunc',
        );
    }

    public function testAutoKeys() {
        foreach (static::sequenceListFromArray(['0', '1', '2', '3', '4']) as $seq) {
            /* @var IEnumerable $seq */

            $arr = $seq->toArray();

            $this->assertEquals(5, count($arr));

            foreach ($arr as $index => $value) {
                $this->assertTrue($index === (int)$value);
                $this->assertTrue($value === (string)$index);
            }
        }
    }

    public function testCustomKeys() {
        foreach ($this->createKeySelectors() as $selector) {
            $seq = static::sequenceFromArray(['a' => 0, 'b' => 1, 'c' => 2]);

            $arr = $seq->toArray($selector);

            $this->assertEquals(3, count($arr));

            $this->assertTrue(isset($arr['A1']));
            $this->assertEquals(0, $arr['A1']);
            $this->assertTrue(isset($arr['B2']));
            $this->assertEquals(1, $arr['B2']);
            $this->assertTrue(isset($arr['C3']));
            $this->assertEquals(2, $arr['C3']);
        }
    }

    public function keySelectorMethod1($key, $item) {
        return keySelectorFunc($key, $item);
    }

    public static function keySelectorMethod2($key, $item) {
        return keySelectorFunc($key, $item);
    }
}
