<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'appendToArray()';


// example #1
$examples[] = new Example();
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq1 = Enumerable::fromValues(1, 2, 3);
$seq2 = Enumerable::create(array("a" => 1, "b" => 2, "c" => 3));

$arr1 = array(11, 22, 33);
$seq1->appendToArray($arr1);

$arr2 = array("a" => 11, 22, 33);
$seq2->appendToArray($arr2, true);

// auto append
echo \'$arr1:\' . "\n";
foreach ($arr1 as $key => $value) {
    echo "\t{$key} => {$value}\n";
}

echo "\n";

// with keys
echo \'$arr2:\' . "\n";
foreach ($arr2 as $key => $value) {
    echo "\t{$key} => {$value}\n";
}

';


require_once './shutdown.inc.php';
