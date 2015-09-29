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


$pageTitle = 'sequenceEqual()';

// example #1
$examples[] = new Example();
$examples[0]->title = 'Default behavior';
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq1 = Enumerable::fromValues(1, 2, 3);
$seq2 = Enumerable::fromValues(1, 2, 3);
$seq3 = Enumerable::fromValues(1, 2);
$seq4 = Enumerable::fromValues(2, 3);
$seq5 = Enumerable::fromValues(6, 7, 8);
$seq6 = Enumerable::create();
$seq7 = Enumerable::create();
$seq8 = Enumerable::fromValues(1, "2", 3);
                                            

$res1 = $seq1->sequenceEqual($seq2);
$res2 = $seq1->reset()
             ->sequenceEqual($seq3);
$res3 = $seq1->reset()
             ->sequenceEqual($seq4);
$res4 = $seq1->reset()
             ->sequenceEqual($seq5);
$res5 = $seq1->reset()
             ->sequenceEqual($seq6);
$res6 = $seq6->reset()
             ->sequenceEqual($seq7);
$res7 = $seq1->reset()
             ->sequenceEqual($seq8);
                
echo "res1 = " . var_export($res1, true);
echo "\n";
echo "res2 = " . var_export($res2, true);
echo "\n";
echo "res3 = " . var_export($res3, true);
echo "\n";
echo "res4 = " . var_export($res4, true);
echo "\n";
echo "res5 = " . var_export($res5, true);
echo "\n";
echo "res6 = " . var_export($res6, true);
echo "\n";
echo "res7 = " . var_export($res7, true);
';

// example #1
$examples[] = new Example();
$examples[1]->title = 'Custom comparer';
$examples[1]->sourceCode = 'use \\System\\Linq\\Enumerable;


$myComparer = function($x, $y) {
    return $x === $y;
};


$seq1 = Enumerable::fromValues(1, 2, 3);
$seq2 = Enumerable::fromValues(1, 2, 3);
$seq3 = Enumerable::fromValues(1, 2);
$seq4 = Enumerable::fromValues(2, 3);
$seq5 = Enumerable::fromValues(6, 7, 8);
$seq6 = Enumerable::create();
$seq7 = Enumerable::create();
$seq8 = Enumerable::fromValues(1, "2", 3);
                        

$res1 = $seq1->sequenceEqual($seq2, $myComparer);
$res2 = $seq1->reset()
             ->sequenceEqual($seq3, $myComparer);
$res3 = $seq1->reset()
             ->sequenceEqual($seq4, $myComparer);
$res4 = $seq1->reset()
             ->sequenceEqual($seq5, $myComparer);
$res5 = $seq1->reset()
             ->sequenceEqual($seq6, $myComparer);
$res6 = $seq6->reset()
             ->sequenceEqual($seq7, $myComparer);
$res7 = $seq1->reset()
             ->sequenceEqual($seq8, $myComparer);


echo "res1 = " . var_export($res1, true);
echo "\n";
echo "res2 = " . var_export($res2, true);
echo "\n";
echo "res3 = " . var_export($res3, true);
echo "\n";
echo "res4 = " . var_export($res4, true);
echo "\n";
echo "res5 = " . var_export($res5, true);
echo "\n";
echo "res6 = " . var_export($res6, true);
echo "\n";
echo "res7 = " . var_export($res7, true);
';


require_once './shutdown.inc.php';
