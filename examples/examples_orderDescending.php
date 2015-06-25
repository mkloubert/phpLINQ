<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'orderDescending()';

// example #1
$examples[] = new Example();
$examples[0]->title = 'Default behavior';
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq = Enumerable::fromValues(3, 5, 1, 4, 2);

$orderedSeq = $seq->orderDescending();
        
foreach ($orderedSeq as $item) {
    echo "{$item}\n";
}
';

// example #2
$examples[] = new Example();
$examples[1]->title = 'Custom algorithm';
$examples[1]->sourceCode = 'use \\System\\Linq\\Enumerable;

$myAlgorithm = function($x, $y) {
    if ($x > $y)
        return -1;
    else if ($x < $y)
        return 1;
    
    return 0;
};
        
$seq = Enumerable::fromValues(3, 5, 1, 4, 2);

$orderedSeq = $seq->orderDescending($myAlgorithm);

foreach ($orderedSeq as $item) {
    echo "{$item}\n";
}
';


require_once './shutdown.inc.php';
