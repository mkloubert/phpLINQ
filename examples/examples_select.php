<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'select()';

// example #1
$examples[] = new Example();
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq = Enumerable::fromValues("TM", "MK", "YS", "JS");

$i = 0;
$newSeq = $seq->select(function($x) use (&$i) {
		                   $result        = new \stdClass();
		                   $result->index = $i++;
		                   $result->value = $x;
		
		                   return $result;
		               });
		
foreach ($newSeq as $item) {
	echo "[{$item->index}]: " . var_export($item->value, true);
	echo "\n";
}
';

require_once './shutdown.inc.php';
