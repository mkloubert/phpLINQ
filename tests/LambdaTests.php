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

use \System\ArgumentException;
use \System\Object;


/**
 * Lambda expression tests.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class LambdaTests extends TestCaseBase {
    public function testEmptyBody1() {
        $lambdas = [
            ' => ',
            '() =>',
            ' => {}',
            '() => {}',
            ' => {

}',
            '() => {
}',
        ];

        foreach ($lambdas as $l) {
            $func = Object::toLambda($l);

            $this->assertSame(null, $func(5979, 23979));
        }
    }

    public function testEmptyBody2() {
        $lambdas = [
            '$x => ',
            '($x) =>',
            '$x => {}',
            '($x) => {}',
            '$x => {

}',
            '($x) => {
}',
        ];

        foreach ($lambdas as $l) {
            $func = Object::toLambda($l);

            $this->assertSame(null, $func(5979, 23979));
        }
    }

    public function testEmptyBody3() {
        $lambdas = [
            '$x, $y => ',
            '($x, $y) =>',
            '$x, $y => {}',
            '($x, $y) => {}',
            '$x, $y => {

}',
            '($x, $y) => {
}',
        ];

        foreach ($lambdas as $l) {
            $func = Object::toLambda($l);

            $this->assertSame(null, $func(5979, 23979));
        }
    }

    public function testInvalidExpressions() {
        $invalidExpressions1 = [
            '($x => trim($x)',
            '$x) => trim($x)',
            '($x => return trim($x);',
            '$x) => return trim($x);',
            '($x => { return trim($x); }',
            '$x) => { return trim($x); }',
            '($x => {
return trim($x);
}',
                        '$x) => {
return trim($x);
}',
        ];

        foreach ($invalidExpressions1 as $expr) {
            unset($lambda);
            unset($thrownEx);

            $this->assertFalse(Object::isLambda($expr));

            try {
                $lambda = Object::toLambda($expr);
            }
            catch (\Exception $ex) {
                $thrownEx = $ex;
            }

            $this->assertFalse(isset($lambda));
            $this->assertTrue(isset($thrownEx));
            $this->assertInstanceOf(ArgumentException::class, $thrownEx);
            $this->assertSame(1, $thrownEx->getCode());
        }

        $invalidExpressions2 = [
            false,
            '',
            true,
            ' ',
            '($x)',
            null,
            new \DateTime(),
            new \stdClass(),
            3.0,
            '$x ',
            1,
            'Das ist das Haus vom Nikolaus.',
            '2',
            "\\trim",
            function() {
                return 1000;
            },
        ];

        foreach ($invalidExpressions2 as $expr) {
            unset($lambda);
            unset($thrownEx);

            $this->assertFalse(Object::isLambda($expr));

            try {
                $lambda = Object::toLambda($expr);
            }
            catch (\Exception $ex) {
                $thrownEx = $ex;
            }

            $this->assertFalse(isset($lambda));
            $this->assertTrue(isset($thrownEx));
            $this->assertInstanceOf(ArgumentException::class, $thrownEx);
            $this->assertSame(0, $thrownEx->getCode());
        }

        $validExpressions = [
            '$x => trim($x)',
            '($x) => trim($x)',
            '$x => return trim($x);',
            '($x) => return trim($x);',
            '$x => { return trim($x); }',
            '($x) => { return trim($x); }',
            '$x => {
return trim($x);
}',
            '($x) => {
return trim($x);
}'
        ];

        foreach ($validExpressions as $expr) {
            unset($lambda);
            unset($thrownEx);

            $this->assertTrue(Object::isLambda($expr));

            try {
                $lambda = Object::toLambda($expr);
            }
            catch (\Exception $ex) {
                $thrownEx = $ex;
            }

            $this->assertTrue(isset($lambda));
            $this->assertFalse(isset($thrownEx));
            $this->assertTrue(is_callable($lambda));
        }
    }

    public function testWith1Argument() {
        $lambdas = [
            '$x => strtoupper($x)',
            '($x) => strtoupper($x)',
            '$x => \strtoupper($x)',
            '($x) => \strtoupper($x)',
            '$x => return strtoupper($x);',
            '($x) => return strtoupper($x);',
            '$x => return \strtoupper($x);',
            '($x) => return \strtoupper($x);',
            '$x => { return strtoupper($x); }',
            '($x) => { return strtoupper($x); }',
            '$x => { return \strtoupper($x); }',
            '($x) => { return \strtoupper($x); }',
            '$x => {
return strtoupper($x);
}',
            '($x) => {
return strtoupper($x);
}',
            '$x => {
return \strtoupper($x);
}',
            '($x) => {
return \strtoupper($x);
}',
        ];

        foreach ($lambdas as $l) {
            $func = Object::toLambda($l);

            $this->assertEquals('ICH BIN DER GEIST, DER STETS VERNEINT',
                                $func('Ich bin der Geist, der stets verneint'));
        }
    }

    public function testWith2Arguments() {
        $lambdas = [
            '$x, $y => $x + $y',
            '($x, $y) => $x + $y',
            '$x, $y => return $x + $y;',
            '($x, $y) => return $x + $y;',
            '$x, $y => { return $x + $y; }',
            '($x, $y) => { return $x + $y; }',
            '$x, $y => {
return $x + $y;
}',
            '($x, $y) => {
return $x + $y;
}',
        ];

        foreach ($lambdas as $l) {
            $func = Object::toLambda($l);

            $this->assertEquals(666,
                                $func(672, -6));
        }
    }

    public function testWith2ArgumentsAndNoResult() {
        $lambdas = [
            '$x, $y => $x + $y;',
            '($x, $y) => $x + $y;',
            '$x, $y => { $x + $y; }',
            '($x, $y) => { $x + $y; }',
        ];

        foreach ($lambdas as $l) {
            $func = Object::toLambda($l);

            $res = $func(672, -6);

            $this->assertTrue(null === $res);
            $this->assertTrue(is_null($res));
        }
    }

    public function testWithNoArguments() {
        $lambdas = [
            '=> 78.9',
            '() => 78.9',
            ' => return 78.9;',
            '() => return 78.9;',
            ' => { return 78.9; }',
            '() => { return 78.9; }',
            ' => {
return 78.9;
}',
            '() => {
return 78.9;
}',
        ];

        foreach ($lambdas as $l) {
            $func = Object::toLambda($l);

            $this->assertSame(78.9, $func());
        }
    }
}
