<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'takeWhile()';

// example #1
$examples[] = new Example();
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq = Enumerable::fromValues(1, 2, 3, 4, 5);

foreach ($seq->takeWhile(function($x) {
                             return $x < 4;
                         }) as $item) {
        
    echo "{$item}\n";
}
';

require_once './shutdown.inc.php';
