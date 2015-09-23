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


function selectorFunc($x) {
    return strtoupper($x);
}

class SelectorClass {
    public function __invoke($x) {
        return strtoupper($x);
    }
}

/**
 * @see \System\Collection\IEnumerable::select()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class SelectTests extends TestCaseBase {
    /**
     * Creates list of selectors.
     *
     * @return array The list of selectors.
     */
    protected function createSelectors() : array {
        return array(
            function($x) {
                return strtoupper($x);
            },
            array($this, 'selector1'),
            array(static::class, 'selector2'),
            new SelectorClass(),
            '$x => strtoupper($x)',
            '($x) => strtoupper($x)',
            '$x => return strtoupper($x);',
            '($x) => return strtoupper($x);',
            '$x => { return strtoupper($x);}',
            '($x) => {return strtoupper($x);}',
            '$x => { return strtoupper($x);
}',
            '($x) => {
return strtoupper($x);
}',
            'selectorFunc',
            '\selectorFunc',
        );
    }

    public function test1() {
        foreach ($this->createSelectors() as $selector) {
            $seq = static::sequenceFromArray(['a', 'B', 'c', 1, 2.0, null, 3.4, 5.60, false]);

            $items = static::sequenceToArray($seq->select($selector));

            $this->assertEquals(9, count($items));
            $this->assertEquals('A', $items[0]);
            $this->assertEquals('B', $items[1]);
            $this->assertEquals('C', $items[2]);
            $this->assertEquals('1', $items[3]);
            $this->assertEquals('2', $items[4]);
            $this->assertEquals('', $items[5]);
            $this->assertEquals('3.4', $items[6]);
            $this->assertEquals('5.6', $items[7]);
            $this->assertEquals('', $items[8]);
        }
    }

    public function selector1($x) {
        return strtoupper($x);
    }

    public static function selector2($x) {
        return strtoupper($x);
    }
}
