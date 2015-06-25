<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'toJson()';

// example #1
$examples[] = new Example();
$examples[0]->title = 'Default behavior';
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq = Enumerable::fromValues(1, 2, 3, 4, 5);

$json = $seq->toJson();
$arr  = json_decode($json, true);


echo \'$json => \' . var_export($json, true) . "\n";
echo "\n";
echo \'$arr => \' . var_export($arr , true) . "\n";
';

// example #2
$examples[] = new Example();
$examples[1]->title = 'Custom key selector';
$examples[1]->sourceCode = 'use \\System\\Linq\\Enumerable;

$myKeySelector = function($i) {
    return "x::" . trim($i);
};
        
$seq = Enumerable::fromValues(1, 2, 3, 4, 5);
        
$json = $seq->toJson(function($key, $item) {
                         return "x::" . trim($key);
                     });
$arr  = json_decode($json, true);


echo \'$json => \' . var_export($json, true) . "\n";
echo "\n";
echo \'$arr => \' . var_export($arr , true) . "\n";
';

require_once './shutdown.inc.php';
