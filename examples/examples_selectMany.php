<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'selectMany()';

// example #1
$examples[] = new Example();
$examples[0]->title = 'Array example';
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq = Enumerable::fromValues(1, 2, 3, 4, 5);

$newSeq = $seq->selectMany(function($x) {
                               return array($x, $x * 10, $x * 100);
                           });
        
foreach ($newSeq as $item) {
    echo "{$item}\n";
}
';


if (isPHP_5_5()) {
    // example #2
    $examples[] = new Example();
    $examples[1]->title = 'Generator example';
    $examples[1]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq = Enumerable::fromValues(6, 7, 8, 9, 10);
    
$newSeq = $seq->selectMany(function($x) {
                               yield $x;
                               yield $x * 10;
                               yield $x * 100;
                           });

foreach ($newSeq as $item) {
    echo "{$item}\n";
}
';
}

require_once './shutdown.inc.php';
