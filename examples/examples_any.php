<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'any()';

// example #1
$examples[] = new Example();
$examples[0]->title = 'Simple example';
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq = Enumerable::fromValues(1, 2, 3);

// no item matches
$res1 = $seq->any(function($x, $ctx) {
                      return $x < 1;
                  });
        
// 2nd item matches
$res2 = $seq->reset()
            ->any(function($x, $ctx) {
                      return $x == 2;
                  });

// default predicate:
// at least one element found
$res3 = $seq->reset()
            ->any();

echo "res1 = " . var_export($res1, true);
echo "\n";
echo "res2 = " . var_export($res2, true);
echo "\n";
echo "res3 = " . var_export($res3, true);
';

// example #2
$examples[] = new Example();
$examples[1]->title = 'Empty sequence';
$examples[1]->description = 'The example shows that empty sequences always return FALSE.';
$examples[1]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq1 = Enumerable::create();
$seq2 = Enumerable::fromValues(1, 2, 3);

// empty
$res1 = $seq1->any(function($x, $ctx) {
                       return $x < 3;
                   });

// where() makes the new sequence empty
$res2 = $seq2->where(function($x) {
                         return is_string($x);
                     })
             ->any(function($x, $ctx) {
                       return $x <= 3;
                   });

// default predicate
$res3 = $seq1->reset()
             ->any();

// default predicate
// with where()
$res4 = $seq2->reset()
             ->where(function($x) {
                         return !is_numeric($x);
                     })
             ->any();

echo "res1 = " . var_export($res1, true);
echo "\n";
echo "res2 = " . var_export($res2, true);
echo "\n";
echo "res3 = " . var_export($res3, true);
echo "\n";
echo "res4 = " . var_export($res4, true);
';

require_once './shutdown.inc.php';
