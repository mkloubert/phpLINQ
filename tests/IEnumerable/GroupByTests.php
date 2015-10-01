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


function groupByKeySelectorFunc($x) : string {
    if (is_numeric($x)) {
        return 'number';
    }

    if (empty($x)) {
        return 'empty';
    }

    if (is_string($x)) {
        return 'string';
    }

    return 'other';
}

class GroupByKeySelectorClass {
    public function __invoke($x) {
        return groupByKeySelectorFunc($x);
    }
}

/**
 * @see \System\Collections\IEnumerable::groupBy()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class GroupByTests extends TestCaseBase {
    /**
     * Creates the key selectors for the tests.
     *
     * @return array The key selectors.
     */
    protected function createKeySelectors() : array {
        return [
            function($x) {
                return groupByKeySelectorFunc($x);
            },
            'groupByKeySelectorFunc',
            '\groupByKeySelectorFunc',
            array($this, 'keySelectorMethod1'),
            array(static::class, 'keySelectorMethod2'),
            new GroupByKeySelectorClass(),
            '$x => groupByKeySelectorFunc($x)',
            '($x) => groupByKeySelectorFunc($x)',
            '$x => \groupByKeySelectorFunc($x)',
            '($x) => \groupByKeySelectorFunc($x)',
            '$x => return groupByKeySelectorFunc($x);',
            '($x) => return groupByKeySelectorFunc($x);',
            '$x => return \groupByKeySelectorFunc($x);',
            '($x) => return \groupByKeySelectorFunc($x);',
            '$x => { return groupByKeySelectorFunc($x); }',
            '($x) => { return groupByKeySelectorFunc($x); }',
            '$x => { return \groupByKeySelectorFunc($x); }',
            '($x) => { return \groupByKeySelectorFunc($x); }',
            '$x => {
return groupByKeySelectorFunc($x);
}',
            '($x) => {
return groupByKeySelectorFunc($x);
}',
            '$x => {
return \groupByKeySelectorFunc($x);
}',
            '($x) => {
return \groupByKeySelectorFunc($x);
}',
        ];
    }

    public function keySelectorMethod1($x) {
        return groupByKeySelectorFunc($x);
    }

    public static function keySelectorMethod2($x) {
        return groupByKeySelectorFunc($x);
    }

    public function test1() {
        foreach ($this->createKeySelectors() as $keySelector) {
            foreach (static::sequenceListFromArray([true, 5979, '', 'TM', false, '23979', 'MK', null]) as $seq) {
                /* @var IEnumerable $seq */

                $items = static::sequenceToArray($seq->groupBy($keySelector), false);

                $this->assertEquals(4, count($items));
                $this->assertSame('other' , $items[0]->key());
                $this->assertSame('number', $items[1]->key());
                $this->assertSame('empty' , $items[2]->key());
                $this->assertSame('string', $items[3]->key());

                $otherItems = static::sequenceToArray($items[0]->getIterator());
                $numberItems = static::sequenceToArray($items[1]->getIterator());
                $emptyItems = static::sequenceToArray($items[2]->getIterator());
                $stringItems = static::sequenceToArray($items[3]->getIterator());

                $this->assertEquals(1, count($otherItems));
                $this->assertTrue($otherItems[0]);

                $this->assertEquals(2, count($numberItems));
                $this->assertEquals(5979, $numberItems[0]);
                $this->assertEquals(23979, $numberItems[1]);

                $this->assertEquals(3, count($emptyItems));
                $this->assertTrue('' === $emptyItems[0]);
                $this->assertTrue(false === $emptyItems[1]);
                $this->assertTrue(null === $emptyItems[2]);

                $this->assertEquals(2, count($stringItems));
                $this->assertEquals('TM', $stringItems[0]);
                $this->assertEquals('MK', $stringItems[1]);
            }
        }
    }
}
