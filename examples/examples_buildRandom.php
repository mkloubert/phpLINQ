<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


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
