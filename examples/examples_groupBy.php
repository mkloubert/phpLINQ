<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'groupBy()';

// example #1
$examples[] = new Example();
$examples[0]->title = 'Default behavior';
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;
    
$seq = Enumerable::fromValues(true, 5979, "", "TM", false, "23979", "MK", null);

$grps = $seq->groupBy(function($item) {
                          if (is_numeric($item)) {
                              return "number";
                          }

                          if (empty($item)) {
                              return "empty";
                          }

                          if (is_string($item)) {
                              return "string";
                          }

                          return "other";
                      });


foreach ($grps as $g) {
    echo "\n";
    echo sprintf("[%s]", $g->key());

    // enumerate elements of current group
    foreach ($g as $item) {
        echo "\n";
        echo "\t" . var_export($item, true);
    }
        
    echo "\n";
}
';

// example #2
$examples[] = new Example();
$examples[1]->title = 'Custom key comparer';
$examples[1]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq = Enumerable::range(1, 10);
        
$myKeyComparer = function($x, $y) {
    return ($x % 2) == ($y % 2);
};
        
$grps = $seq->groupBy(function($item) {
                          return $item % 5;
                      }, $myKeyComparer);


foreach ($grps as $g) {
    echo "\n";
    echo sprintf("[%s]", $g->key());

    // enumerate elements of current group
    foreach ($g as $item) {
        echo "\n";
        echo "\t" . var_export($item, true);
    }

    echo "\n";
}
';


require_once './shutdown.inc.php';
