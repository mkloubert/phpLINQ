<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'except()';

// example #1
$examples[] = new Example();
$examples[0]->title = 'Default behavior';
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;
    
$seq           = Enumerable::fromValues(1, 2, 3, 4, 5);
$excludeThese1 = Enumerable::fromValues(2, "5");
$excludeThese2 = array(1, 3);


$showSequence = function($seq) {
    foreach ($seq as $item) {
        echo "{$item}\n"; 
    }
};


echo "Sequence:\n";
$showSequence($seq->except($excludeThese1));
        
echo "\n";
        
echo "Array:\n";
$showSequence($seq->reset()
                  ->except($excludeThese2));
';

// example #2
$examples[] = new Example();
$examples[1]->title = 'Custom comparer';
$examples[1]->sourceCode = 'use \\System\\Linq\\Enumerable;
    
$seq           = Enumerable::fromValues(1, 2, 3, 4, 5);
$excludeThese1 = Enumerable::fromValues(2, "5");
$excludeThese2 = array("1", 3);


$myComparer = function($x, $y) {
    return $x === $y;
};
        
$showSequence = function($seq) {
    foreach ($seq as $item) {
        echo "{$item}\n"; 
    }
};


echo "Sequence:\n";
$showSequence($seq->except($excludeThese1,
                           $myComparer));
        
echo "\n";
        
echo "Array:\n";
$showSequence($seq->reset()
                  ->except($excludeThese2,
                           $myComparer));
';


require_once './shutdown.inc.php';
