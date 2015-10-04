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


use \System\ValueProvider;


function valueProviderClassProviderFunc() : \DateTime {
    return new DateTime();
}

class valueProviderClassProviderClass {
    public function __invoke() {
        return valueProviderClassProviderFunc();
    }
}


/**
 * Tests for \System\ValueProvider class.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class ValueProviderTests extends TestCaseBase {
    /**
     * Creates the class reflector for the tests.
     *
     * @return ReflectionClass The reflector.
     */
    protected function createClassReflector() : ReflectionClass {
        return new ReflectionClass(ValueProvider::class);
    }

    /**
     * Creates an instance of a \System\ValueProvider based class.
     *
     * @param callable $provider The provider.
     *
     * @return ValueProvider The new instance.
     */
    protected function createInstance($provider) {
        return $this->createClassReflector()
                    ->newInstance($provider);
    }

    /**
     * Creates the value providers for the tests.
     *
     * @return array The created providers.
     */
    protected function createValueProviders() : array {
        return [
            'valueProviderClassProviderFunc',
            '\valueProviderClassProviderFunc',
            function() {
                return valueProviderClassProviderFunc();
            },
            new valueProviderClassProviderClass(),
            [$this, 'valueProviderMethod1'],
            [static::class, 'valueProviderMethod2'],
            '=> valueProviderClassProviderFunc()',
            '=> \valueProviderClassProviderFunc()',
            '() => valueProviderClassProviderFunc()',
            '() => \valueProviderClassProviderFunc()',
            '=> return valueProviderClassProviderFunc();',
            '=> return \valueProviderClassProviderFunc();',
            '() => return valueProviderClassProviderFunc();',
            '() => return \valueProviderClassProviderFunc();',
            '=> { return valueProviderClassProviderFunc(); }',
            '=> { return \valueProviderClassProviderFunc(); }',
            '() => { return valueProviderClassProviderFunc(); }',
            '() => { return \valueProviderClassProviderFunc(); }',
            '=> {
return valueProviderClassProviderFunc();
}',
            '=> {
return \valueProviderClassProviderFunc();
}',
            '() => {
return valueProviderClassProviderFunc();
}',
            '() => {
return \valueProviderClassProviderFunc();
}',
        ];
    }

    public function test1() {
        foreach ($this->createValueProviders() as $provider) {
            /* @var ValueProvider $obj */

            $obj = $this->createInstance($provider);

            $value1 = $obj->getWrappedValue();
            $this->assertInstanceOf(DateTime::class, $value1);

            $value2 = $obj->getWrappedValue();
            $this->assertInstanceOf(DateTime::class, $value2);

            $this->assertNotSame($value1, $value2);
        }
    }

    public function valueProviderMethod1() {
        return static::valueProviderMethod2();
    }

    public static function valueProviderMethod2() {
        return valueProviderClassProviderFunc();
    }
}
