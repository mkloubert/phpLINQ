<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'distinct()';

// example #1
$examples[] = new Example();
$examples[0]->title = 'Default behavior';
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;
    
$seq = Enumerable::fromValues(1, 2, "1", 3);

foreach ($seq->distinct() as $item) {
    echo "{$item}\n"; 
}
';

// example #2
$examples[] = new Example();
$examples[1]->title = 'Custom comparer';
$examples[1]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq = Enumerable::fromValues(1, 2, "1", 2, 3);

$myComparer = function($x, $y) {
    return $x === $y;
};

foreach ($seq->distinct($myComparer) as $item) {
    echo sprintf("[%s] %s\n", gettype($item)
                            , $item);
}
';


require_once './shutdown.inc.php';
