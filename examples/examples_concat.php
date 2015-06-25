<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'aggregate()';


// example #1
$examples[] = new Example();
$examples[0]->description = 'The example shows that empty sequences always return TRUE.';
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq1 = Enumerable::fromValues(1, 2, 3);
$seq2 = Enumerable::create();
$seq3 = Enumerable::fromValues(4, 5, 6);

$concated = $seq1->concat($seq2, $seq3);

foreach ($concated as $item) {
    echo "{$item}\n";
}';


require_once './shutdown.inc.php';
