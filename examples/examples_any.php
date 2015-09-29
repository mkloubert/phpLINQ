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


$pageTitle = 'any()';

// example #1
$examples[] = new Example();
$examples[0]->title = 'Simple example';
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq = Enumerable::fromValues(1, 2, 3);

// no item matches
$res1 = $seq->any(function($x, $ctx) {
                      return $x < 1;
                  });
        
// 2nd item matches
$res2 = $seq->reset()
            ->any(function($x, $ctx) {
                      return $x == 2;
                  });

// default predicate:
// at least one element found
$res3 = $seq->reset()
            ->any();

echo "res1 = " . var_export($res1, true);
echo "\n";
echo "res2 = " . var_export($res2, true);
echo "\n";
echo "res3 = " . var_export($res3, true);
';

// example #2
$examples[] = new Example();
$examples[1]->title = 'Empty sequence';
$examples[1]->description = 'The example shows that empty sequences always return FALSE.';
$examples[1]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq1 = Enumerable::create();
$seq2 = Enumerable::fromValues(1, 2, 3);

// empty
$res1 = $seq1->any(function($x, $ctx) {
                       return $x < 3;
                   });

// where() makes the new sequence empty
$res2 = $seq2->where(function($x) {
                         return is_string($x);
                     })
             ->any(function($x, $ctx) {
                       return $x <= 3;
                   });

// default predicate
$res3 = $seq1->reset()
             ->any();

// default predicate
// with where()
$res4 = $seq2->reset()
             ->where(function($x) {
                         return !is_numeric($x);
                     })
             ->any();

echo "res1 = " . var_export($res1, true);
echo "\n";
echo "res2 = " . var_export($res2, true);
echo "\n";
echo "res3 = " . var_export($res3, true);
echo "\n";
echo "res4 = " . var_export($res4, true);
';

require_once './shutdown.inc.php';
