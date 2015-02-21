<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'defaultIfEmpty()';

// example #1
$examples[] = new Example();
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;
	
$seq1 = Enumerable::fromValues(1, 2, 3);
$seq2 = Enumerable::createEmpty();


$showSequence = function($seq) {
    foreach ($seq->defaultIfEmpty("TM", "MK") as $item) {
        echo "{$item}\n"; 
    }
};


// not empty
echo "seq1 (1):\n";
$showSequence($seq1);

echo "\n";

// empty, so take default values
echo "seq2:\n";
$showSequence($seq2);
		
echo "\n";

// where() makes new sequence empty
echo "seq1 (2):\n";
$showSequence($seq1->reset()
                   ->where(function($x) {
                               return $x > 3;
                           }));
';


require_once './shutdown.inc.php';
