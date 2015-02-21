<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'toDictionary()';

// example #1
$examples[] = new Example();
$examples[0]->title = 'Default behavior';
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq = Enumerable::fromValues(1, 2, 3, 4, 5);

$dict      = $seq->toDictionary();
$dict[1]   = "TM";
$dict[4.0] = "1979-09-05";

foreach ($dict->keys() as $key) {
	echo "[{$key}] = " . var_export($dict[$key], true) . "\n";
}
';

// example #2
$examples[] = new Example();
$examples[1]->title = 'Custom key selector';
$examples[1]->sourceCode = 'use \\System\\Linq\\Enumerable;

$myKeySelector = function($orgKey, $item) {
	return "key::" . trim($orgKey);
};
		
$seq = Enumerable::fromValues(1, 2, 3, 4, 5);

$dict = $seq->toDictionary($myKeySelector);
		
foreach ($dict->keys() as $key) {
	echo "[{$key}] = " . var_export($dict[$key], true) . "\n";
}
';

// example #3
$examples[] = new Example();
$examples[2]->title = 'Custom key comparer';
$examples[2]->sourceCode = 'use \\System\\Linq\\Enumerable;

$myKeyComparer = function($x, $y) {
	return $x === $y;
};

$seq = Enumerable::fromValues(1, 2, 3, 4, 5);

$dict      = $seq->toDictionary(null, $myKeyComparer);
$dict[1]   = "TM";
$dict[4.0] = "1979-09-05";
		
foreach ($dict->keys() as $key) {
	echo "[{$key}] = " . var_export($dict[$key], true) . "\n";
}
';


require_once './shutdown.inc.php';
