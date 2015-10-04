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

require_once __DIR__ . DIRECTORY_SEPARATOR . 'ComparerTests.php';

use \System\Comparer;
use \System\DescendingComparer;


/**
 * Tests for \System\DescendingComparer class.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class DescendingComparerTests extends ComparerTests {
    /**
     * {@inheritDoc}
     */
    protected function createClassReflector() : ReflectionClass {
        return new ReflectionClass(DescendingComparer::class);
    }

    public function testComparer1() {
        $objs = $this->createComparerObjects();

        usort($objs, function(Comparer $x, Comparer $y) : int {
            return $x->compareTo($y);
        });

        $this->assertEquals(4, count($objs));

        $values = array_map(function(Comparer $obj) {
            return $obj->getWrappedValue();
        }, $objs);

        $this->assertEquals(count($objs), count($values));

        for ($i = 0; $i < count($values); $i++) {
            $this->assertSame(count($values) - $i, $values[$i]);
        }
    }

    public function testComparer2() {
        $objs = $this->createComparerObjects();

        usort($objs, function(Comparer $x, Comparer $y) : int {
            return $y->compareTo($x);
        });

        $this->assertEquals(4, count($objs));

        $values = array_map(function(Comparer $obj) {
            return $obj->getWrappedValue();
        }, $objs);

        $this->assertEquals(count($objs), count($values));

        for ($i = 0; $i < count($values); $i++) {
            $this->assertSame($i + 1, $values[$i]);
        }
    }
}
