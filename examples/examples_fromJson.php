<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'fromJson()';

// example #1
$examples[] = new Example();
$examples[0]->title = 'Default behavior';
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;


$json = \'{"a": 1, "b": 2, "c": 3, "d": 4, "e": 5}\';
$seq  = Enumerable::fromJson($json);


foreach ($seq as $key => $item) {
    echo "{$key} => {$item}\n";
}
';


require_once './shutdown.inc.php';
