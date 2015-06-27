<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'range()';


// example #1
$examples[] = new Example();
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

// 1 - 10
$seq = Enumerable::range(1, 10);

foreach ($seq as $item) {
    echo "{$item}\n";
}
';

// example #2
$examples[] = new Example();
$examples[1]->title      = 'Custom step value';
$examples[1]->sourceCode = 'use \\System\\Linq\\Enumerable;

// 2 - 4
$seq = Enumerable::range(2, 5, 0.5);

foreach ($seq as $item) {
    echo "{$item}\n";
}
';

// example #3
$examples[] = new Example();
$examples[2]->title      = 'Function that provides the custom step value';
$examples[2]->sourceCode = 'use \\System\\Linq\\Enumerable;

// 3 - 5.25
$seq = Enumerable::range(3, 10,
                         function($result, $ctx) {
                             // $ctx => s. build()

                             // step value
                             return 0.25;
                         });

foreach ($seq as $item) {
    echo "{$item}\n";
}
';


require_once './shutdown.inc.php';
