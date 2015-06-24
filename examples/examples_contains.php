<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'contains()';

// example #1
$examples[] = new Example();
$examples[0]->title = 'Default behavior';
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;
    
$seq1 = Enumerable::fromValues(1, 2, 3);
$seq2 = Enumerable::create();


// exists
$res1 = $seq1->contains(1);
// does not exist
$res2 = $seq1->reset()
             ->contains(4);
// exists as integer value
$res3 = $seq1->reset()
             ->contains("2");
// empty
$res4 = $seq2->contains(null);

echo "res1: " . var_export($res1, true);
echo "\n";
echo "res2: " . var_export($res2, true);
echo "\n";
echo "res3: " . var_export($res3, true);
echo "\n";
echo "res4: " . var_export($res4, true);
';

// example #2
$examples[] = new Example();
$examples[1]->title = 'Custom comparer';
$examples[1]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq = Enumerable::fromValues(1, 2, 3);

$myComparer = function($x, $y) {
    return $x === $y;
};
        

// exists
$res1 = $seq->contains(1, $myComparer);
// search value is STRING not integer
$res2 = $seq->reset()
            ->contains("1", $myComparer);
// search value is FLOAT not integer
$res3 = $seq->reset()
            ->contains(1.0, $myComparer);


echo "res1: " . var_export($res1, true);
echo "\n";
echo "res2: " . var_export($res2, true);
echo "\n";
echo "res3: " . var_export($res3, true);
';


require_once './shutdown.inc.php';
