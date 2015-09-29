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


$pageTitle = 'buildRandom()';


// example #1
$examples[] = new Example();
$examples[0]->title = 'Password generator';
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

$pwdChars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

$pwd = Enumerable::buildRandom(16)
                 ->select(function($x) use ($pwdChars) {
                              return $pwdChars[$x % strlen($pwdChars)];
                          })
                 ->concatToString();

echo "Your password: " . $pwd;
';

// example #2
$examples[] = new Example();
$examples[1]->title = 'Maxmimum value';
$examples[1]->sourceCode = 'use \\System\\Linq\\Enumerable;

// build 10 random values
$seq = Enumerable::buildRandom(10, 10);

foreach ($seq as $item) {
    echo "{$item}\n";
}
';

// example #3
$examples[] = new Example();
$examples[2]->title = 'Minimum value';
$examples[2]->sourceCode = 'use \\System\\Linq\\Enumerable;

// build 10 random values
// between 90 and 100
$seq = Enumerable::buildRandom(10, 100, 90);

foreach ($seq as $item) {
    echo "{$item}\n";
}
';

// example #4
$examples[] = new Example();
$examples[3]->title = 'Custom seeder';
$examples[3]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq = Enumerable::buildRandom(10, function() {
                                       list($usec, $sec) = explode(" ", microtime());
                                       $seed = (float) $sec + ((float) $usec * 100000);

                                       mt_srand($seed);
                                   });

foreach ($seq as $item) {
    echo "{$item}\n";
}
';


require_once './shutdown.inc.php';
