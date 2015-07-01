<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'serialize()';

// example #1
$examples[] = new Example();
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

$seq = Enumerable::create(array("PZ" => 19861222,
                                "TM" => 19790905,
                                "MK" => "1979-09-23",
                                "YS" => 19810701,
                                "JS" => 19791224));

$serialized = serialize($seq);
$unserialized = unserialize($serialized);
        
foreach ($unserialized as $key => $item) {
    echo "{$key} => {$item}";
    echo "\n";
}
';

require_once './shutdown.inc.php';
