<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'toArray()';

// example #1
$examples[] = new Example();
$examples[0]->title = 'Default behavior';
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq = Enumerable::fromValues(1, 2, 3, 4, 5);

$arr = $seq->toArray();
		
echo var_export($arr);
';

// example #2
$examples[] = new Example();
$examples[1]->title = 'Custom key selector';
$examples[1]->sourceCode = 'use \\System\\Linq\\Enumerable;

$myKeySelector = function($i) {
	return "x::" . trim($i);
};
		
$seq = Enumerable::fromValues(1, 2, 3, 4, 5);
		
$arr = $seq->toArray(function($index, $item) {
	                     return "x::" . trim($index);
                     });
		
echo var_export($arr);
';

require_once './shutdown.inc.php';
