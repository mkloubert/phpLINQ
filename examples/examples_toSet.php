<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'toSet()';

// example #1
$examples[] = new Example();
$examples[0]->title = 'Default behavior';
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq = Enumerable::fromValues(1, 2, 3, 4, 5, 4);

$set = $seq->toSet();
        
foreach ($set as $item) {
    echo "{$item}\n";
}
';

// example #2
$examples[] = new Example();
$examples[1]->title = 'Custom comparer';
$examples[1]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq = Enumerable::fromValues(1, 2, 3, 4, 5, "4");

$set = $seq->toSet(function($x, $y) {
                       return $x === $y;
                   });

foreach ($set as $item) {
    echo "{$item}\n";
}
';


require_once './shutdown.inc.php';
