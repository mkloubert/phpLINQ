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

use \System\Collections\IIndexedItemContext;
use \System\Collections\IEnumerable;


function joinToStringCallbackSeparatorFactoryFunc($x, IIndexedItemContext $ctx) : string {
    return !$ctx->isLast() ? ', ' : ' and ';
}

class JoinToStringSeparatorFactoryClass {
    public function __invoke($x, IIndexedItemContext $ctx) {
        return joinToStringCallbackSeparatorFactoryFunc($x, $ctx);
    }
}

/**
 * @see \System\Collections\IEnumerable::joinToStringCallback()
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class JoinToStringCallbackTests extends TestCaseBase {
    /**
     * Creates the separator factories for the tests.
     *
     * @return array The separator factories.
     */
    protected function createSeparatorFactories() : array {
        return [
            function($x, IIndexedItemContext $ctx) {
                return joinToStringCallbackSeparatorFactoryFunc($x, $ctx);
            },
            'joinToStringCallbackSeparatorFactoryFunc',
            '\joinToStringCallbackSeparatorFactoryFunc',
            array($this, 'separatorFactoryMethod1'),
            array(static::class, 'separatorFactoryMethod2'),
            new JoinToStringSeparatorFactoryClass(),
            '$x, $ctx => joinToStringCallbackSeparatorFactoryFunc($x, $ctx)',
            '($x, $ctx) => joinToStringCallbackSeparatorFactoryFunc($x, $ctx)',
            '$x, $ctx => return joinToStringCallbackSeparatorFactoryFunc($x, $ctx);',
            '($x, $ctx) => return joinToStringCallbackSeparatorFactoryFunc($x, $ctx);',
            '$x, $ctx => { return joinToStringCallbackSeparatorFactoryFunc($x, $ctx); }',
            '($x, $ctx) => { return joinToStringCallbackSeparatorFactoryFunc($x, $ctx); }',
            '$x, $ctx => {
return joinToStringCallbackSeparatorFactoryFunc($x, $ctx);
}',
            '($x, $ctx) => {
return joinToStringCallbackSeparatorFactoryFunc($x, $ctx);
}',
            '$x, $ctx => \joinToStringCallbackSeparatorFactoryFunc($x, $ctx)',
            '($x, $ctx) => \joinToStringCallbackSeparatorFactoryFunc($x, $ctx)',
            '$x, $ctx => return \joinToStringCallbackSeparatorFactoryFunc($x, $ctx);',
            '($x, $ctx) => return \joinToStringCallbackSeparatorFactoryFunc($x, $ctx);',
            '$x, $ctx => { return \joinToStringCallbackSeparatorFactoryFunc($x, $ctx); }',
            '($x, $ctx) => { return \joinToStringCallbackSeparatorFactoryFunc($x, $ctx); }',
            '$x, $ctx => {
return \joinToStringCallbackSeparatorFactoryFunc($x, $ctx);
}',
            '($x, $ctx) => {
return \joinToStringCallbackSeparatorFactoryFunc($x, $ctx);
}',
        ];
    }

    public function separatorFactoryMethod1($x, IIndexedItemContext $ctx) {
        return joinToStringCallbackSeparatorFactoryFunc($x, $ctx);
    }

    public static function separatorFactoryMethod2($x, IIndexedItemContext $ctx) {
        return joinToStringCallbackSeparatorFactoryFunc($x, $ctx);
    }

    public function test1() {
        foreach ($this->createSeparatorFactories() as $separatorFactory) {
            foreach (static::sequenceListFromArray(['Tanja', 'Yvonne', 'Julia', 'Marcel']) as $seq) {
                /* @var IEnumerable $seq */

                $str = $seq->joinToStringCallback($separatorFactory);

                $this->assertNotEquals('Tanja, Yvonne, Julia, Marcel', $str);
                $this->assertEquals('Tanja, Yvonne, Julia and Marcel', $str);
            }
        }
    }

    public function test2() {
        foreach ($this->createSeparatorFactories() as $separatorFactory) {
            foreach (static::sequenceListFromArray([]) as $seq) {
                /* @var IEnumerable $seq */

                $str = $seq->joinToStringCallback($separatorFactory, 'Phantasie ist wichtiger als Wissen, denn Wissen ist begrenzt.');

                $this->assertEquals('Phantasie ist wichtiger als Wissen, denn Wissen ist begrenzt.', $str);
            }
        }
    }
}
