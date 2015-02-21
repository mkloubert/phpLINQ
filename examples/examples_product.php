<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'multiply()';

// example #1
$examples[] = new Example();
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;
	
$seq1 = Enumerable::fromValues(1, 2, 3, 4, 5);
$seq2 = Enumerable::createEmpty();

$res1 = $seq1->product();
// empty sequence
$res2 = $seq2->product("TM");
		
echo "res1: {$res1}\n";
echo "res2: {$res2}";
';


require_once './shutdown.inc.php';
