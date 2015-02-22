<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'toList()';

// example #1
$examples[] = new Example();
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq = Enumerable::fromValues(1, 2, 3, 4, 5);

$list = $seq->toList();
		
$res1 = isset($list[0]);  // first
$res2 = isset($list[5]);  // does NOT exist
$res3 = count($list);
		
$list->add("TM");
$res4 = isset($list[5]);  // now exists
$res5 = count($list);
		
$list->removeAt(3);
$res6 = $list->reset()->stringJoin(", ");
        
echo "res1 = " . var_export($res1, true);
echo "\n";
echo "res2 = " . var_export($res2, true);
echo "\n";
echo "res3 = " . var_export($res3, true);
echo "\n";
echo "res4 = " . var_export($res4, true);
echo "\n";
echo "res5 = " . var_export($res5, true);
echo "\n";
echo "res6 = " . var_export($res6, true);
';


require_once './shutdown.inc.php';
