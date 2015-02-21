<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'count()';

// example #1
$examples[] = new Example();
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;
	
$seq1 = Enumerable::fromValues(1, 2, 3);
$seq2 = Enumerable::createEmpty();

		
$res1 = $seq1->count();
$res2 = $seq2->count();

$seq1->reset();
$seq2->reset();
		
// BETTER WAY to do this
$res3 = count($seq1);
$res4 = count($seq2);

echo "res1: " . var_export($res1, true);
echo "\n";
echo "res2: " . var_export($res2, true);
echo "\n";
echo "res3: " . var_export($res3, true);
echo "\n";
echo "res4: " . var_export($res4, true);
';


require_once './shutdown.inc.php';
