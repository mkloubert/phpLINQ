<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'aggregate()';


// example #1
$examples[] = new Example();
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq1 = Enumerable::fromValues(1, 2, 3);
$seq2 = Enumerable::create();

$sumAccumulator = function($result, $item, $ctx) {
    return $result + $item;
};

$res1 = $seq1->aggregate($sumAccumulator, "TM");

// empty
$res2 = $seq2->aggregate($sumAccumulator, "TM");

echo "res1 = " . var_export($res1, true);
echo "\n";
echo "res2 = " . var_export($res2, true);
';


require_once './shutdown.inc.php';
