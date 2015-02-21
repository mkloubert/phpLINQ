<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'sequenceEqual()';

// example #1
$examples[] = new Example();
$examples[0]->title = 'Default behavior';
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq1 = Enumerable::fromValues(1, 2, 3);
$seq2 = Enumerable::fromValues(1, 2, 3);
$seq3 = Enumerable::fromValues(1, 2);
$seq4 = Enumerable::fromValues(2, 3);
$seq5 = Enumerable::fromValues(6, 7, 8);
$seq6 = Enumerable::createEmpty();
$seq7 = Enumerable::createEmpty();
$seq8 = Enumerable::fromValues(1, "2", 3);
											

$res1 = $seq1->sequenceEqual($seq2);
$res2 = $seq1->reset()
		     ->sequenceEqual($seq3);
$res3 = $seq1->reset()
		     ->sequenceEqual($seq4);
$res4 = $seq1->reset()
		     ->sequenceEqual($seq5);
$res5 = $seq1->reset()
		     ->sequenceEqual($seq6);
$res6 = $seq6->reset()
		     ->sequenceEqual($seq7);
$res7 = $seq1->reset()
		     ->sequenceEqual($seq8);
				
echo "res1 = " . var_export($res1, true);
echo "\n";
echo "res2 = " . var_export($res2, true);
echo "\n";
echo "res3 = " . var_export($res3, true);
echo "\n";
echo "res4 = " . var_export($res4, true);
echo "\n";
echo "res5 = " . var_export($res5, true);
echo "\n";
echo "res6 = " . var_export($res6, true);
echo "\n";
echo "res7 = " . var_export($res7, true);
';

// example #1
$examples[] = new Example();
$examples[1]->title = 'Default behavior';
$examples[1]->sourceCode = 'use \\System\\Linq\\Enumerable;


$myComparer = function($x, $y) {
	return $x === $y;
};


$seq1 = Enumerable::fromValues(1, 2, 3);
$seq2 = Enumerable::fromValues(1, 2, 3);
$seq3 = Enumerable::fromValues(1, 2);
$seq4 = Enumerable::fromValues(2, 3);
$seq5 = Enumerable::fromValues(6, 7, 8);
$seq6 = Enumerable::createEmpty();
$seq7 = Enumerable::createEmpty();
$seq8 = Enumerable::fromValues(1, "2", 3);
						

$res1 = $seq1->sequenceEqual($seq2, $myComparer);
$res2 = $seq1->reset()
		     ->sequenceEqual($seq3, $myComparer);
$res3 = $seq1->reset()
		     ->sequenceEqual($seq4, $myComparer);
$res4 = $seq1->reset()
		     ->sequenceEqual($seq5, $myComparer);
$res5 = $seq1->reset()
		     ->sequenceEqual($seq6, $myComparer);
$res6 = $seq6->reset()
		     ->sequenceEqual($seq7, $myComparer);
$res7 = $seq1->reset()
		     ->sequenceEqual($seq8, $myComparer);


echo "res1 = " . var_export($res1, true);
echo "\n";
echo "res2 = " . var_export($res2, true);
echo "\n";
echo "res3 = " . var_export($res3, true);
echo "\n";
echo "res4 = " . var_export($res4, true);
echo "\n";
echo "res5 = " . var_export($res5, true);
echo "\n";
echo "res6 = " . var_export($res6, true);
echo "\n";
echo "res7 = " . var_export($res7, true);
';


require_once './shutdown.inc.php';
