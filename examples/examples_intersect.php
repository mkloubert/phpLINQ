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


$pageTitle = 'intersect()';

// example #1
$examples[] = new Example();
$examples[0]->title = 'Default behavior';
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;
    
$seq1 = Enumerable::fromValues(1, 2, 3, 4, 5);
$seq2 = Enumerable::fromValues(3, 2);
$seq3 = Enumerable::fromValues(6, 7);
$seq4 = Enumerable::create();
$seq5 = Enumerable::fromValues("2", 5);
        

$showSequence = function($seq) {
    foreach ($seq as $item) {
        echo "{$item}\n";
    }
};


echo "seq1 + seq2:\n";
$showSequence($seq1->intersect($seq2));
        
echo "\n";
        
echo "seq1 + seq3:\n";
$showSequence($seq1->reset()
                   ->intersect($seq3));

echo "\n";
        
echo "seq1 + seq4:\n";
$showSequence($seq1->reset()
                   ->intersect($seq4));
        
echo "\n";
        
echo "seq1 + seq5:\n";
$showSequence($seq1->reset()
                   ->intersect($seq5));
';

// example #2
$examples[] = new Example();
$examples[1]->title = 'Custom comparer';
$examples[1]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq1 = Enumerable::fromValues(1, 2, 3, 4, 5);
$seq2 = Enumerable::fromValues(3, 2);
$seq3 = Enumerable::fromValues(6, 7);
$seq4 = Enumerable::create();
$seq5 = Enumerable::fromValues("2", 5);


$myComparer = function($x, $y) {
    return $x === $y;
};

$showSequence = function($seq) {
    foreach ($seq as $item) {
        echo "{$item}\n";
    }
};


echo "seq1 + seq2:\n";
$showSequence($seq1->intersect($seq2, $myComparer));

echo "\n";

echo "seq1 + seq3:\n";
$showSequence($seq1->reset()
                   ->intersect($seq3, $myComparer));

echo "\n";

echo "seq1 + seq4:\n";
$showSequence($seq1->reset()
                   ->intersect($seq4, $myComparer));
        
echo "\n";
        
echo "seq1 + seq5:\n";
$showSequence($seq1->reset()
                   ->intersect($seq5, $myComparer));
';


require_once './shutdown.inc.php';
