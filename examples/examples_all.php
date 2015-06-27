<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'all()';

// example #1
$examples[] = new Example();
$examples[0]->title = 'Simple example';
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq = Enumerable::fromValues(1, 2, 3);

// 3rd item does not match
$res1 = $seq->all(function($x, $ctx) {
                      return $x < 3;
                  });
        
// all items match
$res2 = $seq->reset()
            ->all(function($x, $ctx) {
                      return $x < 4;
                  });
            
echo "res1 = " . var_export($res1, true);
echo "\n";
echo "res2 = " . var_export($res2, true);
';

// example #2
$examples[] = new Example();
$examples[1]->title = 'Empty sequence';
$examples[1]->description = 'The example shows that empty sequences always return TRUE.';
$examples[1]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq1 = Enumerable::create();
$seq2 = Enumerable::fromValues(1, 2, 3);

// empty
$res1 = $seq1->all(function($x, $ctx) {
                       return $x < 3;
                   });

// where() makes the new sequence empty
$res2 = $seq2->where(function($x) {
                         return is_string($x);
                     })
             ->all(function($x, $ctx) {
                       return $x <= 3;
                   });

echo "res1 = " . var_export($res1, true);
echo "\n";
echo "res2 = " . var_export($res2, true);
';

require_once './shutdown.inc.php';
