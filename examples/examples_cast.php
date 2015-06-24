<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'cast()';

// example #1
$examples[] = new Example();
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

$showSeqence = function($seq) {
    foreach ($seq as $item) {
        echo sprintf("[%s] %s", gettype($item)
                              , $item);
        echo "\n";
    }
};
        
// sequence of integers
$intSeq = Enumerable::fromValues(1, 2, 3);
        
echo "intSeq:\n";
$showSeqence($intSeq);

echo "\n";

// cast to strings
echo "strSeq:\n";
$showSeqence($intSeq->reset()
                    ->cast("string"));
';

require_once './shutdown.inc.php';
