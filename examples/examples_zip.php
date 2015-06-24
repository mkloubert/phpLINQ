<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'zip()';

// example #1
$examples[] = new Example();
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq1 = Enumerable::fromValues(1, 2, 3, 4);
$seq2 = Enumerable::fromValues("one", "two", "three");

$zipped = $seq1->zip($seq2, function($x, $y) {
                                return sprintf("%s %s",
                                               $x, $y);
                            });

foreach ($zipped as $item) {
    echo "{$item}\n";
}
';


require_once './shutdown.inc.php';
