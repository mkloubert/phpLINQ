<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'union()';

// example #1
$examples[] = new Example();
$examples[0]->title = 'Default behavior'; 
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq1 = Enumerable::fromValues(5, 3, 9, 7, 5, 9, 3, 7);
$seq2 = Enumerable::fromValues(8, 3, 6, 4, 4, 9, 1, 0);

foreach ($seq1->union($seq2) as $item) {
    echo "{$item}\n";
}
';

// example #2
$examples[] = new Example();
$examples[1]->title = 'Custom comparer';
$examples[1]->sourceCode = 'use \\System\\Linq\\Enumerable;

$myComparer = function($x, $y) {
    return $x === $y;
};
        
$seq1 = Enumerable::fromValues(5, 3, 9, 7, 5, 9, 3, 7);
$seq2 = Enumerable::fromValues(8, 3, 6, 4, "4", 9, 1, 0);

foreach ($seq1->union($seq2, $myComparer) as $item) {
    echo var_export($item, true) . "\n";
}
';


require_once './shutdown.inc.php';
