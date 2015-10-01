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


require_once './bootstrap.inc.php';


$pageTitle = 'appendToArray()';


// example #1
$examples[] = new Example();
$examples[0]->title = 'PHP array';
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq1 = Enumerable::fromValues(1, 2, 3);
$seq2 = Enumerable::create(["a" => 1, "b" => 2, "c" => 3]);

// auto append
$arr1 = [11, 22, 33];
$seq1->appendToArray($arr1);

// with keys
$arr2 = ["a" => 11, 22, 33];
$seq2->appendToArray($arr2, true);


echo \'$arr1:\' . "\n";
foreach ($arr1 as $key => $value) {
    echo "\t{$key} => {$value}\n";
}

echo "\n";

echo \'$arr2:\' . "\n";
foreach ($arr2 as $key => $value) {
    echo "\t{$key} => {$value}\n";
}
';

// example #2
$examples[] = new Example();
$examples[1]->title = 'ArrayAccess object';
$examples[1]->sourceCode = 'use \\System\\Linq\\Enumerable;

class MyArrayWrapper implements \ArrayAccess {
    private $_array;

    public function __construct(array $arr) {
        $this->_array = $arr;
    }

    public function getArray() : array {
        return $this->_array;
    }

    public function offsetExists($key) {
        return \array_key_exists($key, $this->_array);
    }

    public function offsetGet($key) {
        return $this->_array[$key];
    }

    public function offsetSet($key, $newValue) {
        if (null !== $key) {
            $this->_array[$key] = $newValue;
        }
        else {
            $this->_array[] = $newValue;
        }
    }

    public function offsetUnset($key) {
        unset($this->_array[$key]);
    }
}

$seq1 = Enumerable::fromValues(1, 2, 3);
$seq2 = Enumerable::create(["A" => 1, "B" => 2, "C" => 3]);

// auto append
$arrObj1 = new MyArrayWrapper([111, 222, 333]);
$seq1->appendToArray($arrObj1);

// with keys
$arrObj2 = new MyArrayWrapper(["A" => 111, 222, 333]);
$seq2->appendToArray($arrObj2, true);


echo \'$arrObj1:\' . "\n";
foreach ($arrObj1->getArray() as $key => $value) {
    echo "\t{$key} => {$value}\n";
}

echo "\n";

echo \'$arrObj2:\' . "\n";
foreach ($arrObj2->getArray() as $key => $value) {
    echo "\t{$key} => {$value}\n";
}
';

require_once './shutdown.inc.php';
