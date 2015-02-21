<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'singleOrDefault()';

// example #1
$examples[] = new Example();
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;
	
$seq1 = Enumerable::fromValues(1);
$seq2 = Enumerable::createEmpty();
$seq3 = Enumerable::fromValues(1, 2);

$res1 = $seq1->singleOrDefault("TM");
// no item matches
$res2 = $seq1->reset()
		     ->singleOrDefault(function($x) {
		                           return $x > 3;
		                       }, "TM");
// more than one element
try {
	$res3 = $seq3->singleOrDefault();
}
catch (\Exception $ex) {
	$res3 = "EXCEPTION: " . $ex->getMessage();
}

echo "res1: " . var_export($res1, true);
echo "\n";
echo "res2: " . var_export($res2, true);
echo "\n";
echo "res3: " . var_export($res3, true);
';


require_once './shutdown.inc.php';
