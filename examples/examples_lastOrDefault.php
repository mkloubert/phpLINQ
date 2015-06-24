<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'lastOrDefault()';

// example #1
$examples[] = new Example();
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;
    
$seq1 = Enumerable::fromValues(1, 2, 3);
$seq2 = Enumerable::create();


$res1 = $seq1->lastOrDefault("TM");
// no item matches
$res2 = $seq1->reset()
             ->lastOrDefault(function($x) {
                                 return $x > 3;
                             }, "TM");
// empty (NULL is default value)
$res3 = $seq2->lastOrDefault();


echo "res1: " . var_export($res1, true);
echo "\n";
echo "res2: " . var_export($res2, true);
echo "\n";
echo "res3: " . var_export($res3, true);
';


require_once './shutdown.inc.php';
