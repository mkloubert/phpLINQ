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


$pageTitle = 'groupBy()';

// example #1
$examples[] = new Example();
$examples[0]->title = 'Default behavior';
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;
    
$seq = Enumerable::fromValues(true, 5979, "", "TM", false, "23979", "MK", null);

$grps = $seq->groupBy(function($item) {
                          if (is_numeric($item)) {
                              return "number";
                          }

                          if (empty($item)) {
                              return "empty";
                          }

                          if (is_string($item)) {
                              return "string";
                          }

                          return "other";
                      });


foreach ($grps as $g) {
    echo "\n";
    echo sprintf("[%s]", $g->key());

    // enumerate elements of current group
    foreach ($g as $item) {
        echo "\n";
        echo "\t" . var_export($item, true);
    }
        
    echo "\n";
}
';

// example #2
$examples[] = new Example();
$examples[1]->title = 'Custom key comparer';
$examples[1]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq = Enumerable::range(1, 10);
        
$myKeyComparer = function($x, $y) {
    return ($x % 2) == ($y % 2);
};
        
$grps = $seq->groupBy(function($item) {
                          return $item % 5;
                      }, $myKeyComparer);


foreach ($grps as $g) {
    echo "\n";
    echo sprintf("[%s]", $g->key());

    // enumerate elements of current group
    foreach ($g as $item) {
        echo "\n";
        echo "\t" . var_export($item, true);
    }

    echo "\n";
}
';


require_once './shutdown.inc.php';
