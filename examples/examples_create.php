<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'create()';


// example #1
$examples[] = new Example();
$examples[0]->title      = 'Array';
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

// 1 - 5
$seq = Enumerable::create(array(1, 2, 3, 4, 5));

foreach ($seq as $item) {
    echo "{$item}\n";
}
';

// example #2
$examples[] = new Example();
$examples[1]->title      = 'Iterator';
$examples[1]->sourceCode = 'use \\System\\Linq\\Enumerable;

$it  = new \ArrayIterator(array(11, 22, 33, 44, 55));
$seq = Enumerable::create($it);

foreach ($seq as $item) {
    echo "{$item}\n";
}
';

if (isPHP_5_5()) {
    // example #1
    $examples[] = new Example();
    $examples[2]->title      = 'Generator';
    $examples[2]->sourceCode = 'use \\System\\Linq\\Enumerable;

$createItems = function() {
    yield 555;
    yield 444;
    yield 333;
    yield 222;
    yield 111;
};

$gen = $createItems();
$seq = Enumerable::create($gen);

foreach ($seq as $item) {
    echo "{$item}\n";
}
';

}


require_once './shutdown.inc.php';
