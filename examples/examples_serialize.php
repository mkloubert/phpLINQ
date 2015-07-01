<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'serialize()';

// example #1
$examples[] = new Example();
$examples[0]->title = 'Sequence';
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

// example #2
$examples[] = new Example();
$examples[1]->title = 'Collection';
$examples[1]->sourceCode = 'use \\System\\Collections\\Collection;


$coll = new Collection();
$coll->add("PZ");
$coll->add("TM");
$coll->add("MK");
$coll->add("YS");
$coll->add("JS");


$serialized = serialize($coll);
$unserialized = unserialize($serialized);

foreach ($unserialized as $item) {
    echo $item;
    echo "\n";
}
';

// example #3
$examples[] = new Example();
$examples[2]->title = 'Dictionary';
$examples[2]->sourceCode = 'use \\System\\Collections\\Dictionary;


$dict = new Dictionary();
$dict->add("PZ", 19861222);
$dict->add("TM", 19790905);
$dict->add("MK", "1979-09-23");
$dict->add("YS", 19810701);
$dict->add("JS", 19791224);


$serialized = serialize($dict);
$unserialized = unserialize($serialized);

foreach ($unserialized as $entry) {
    echo $entry->key() . " => " . $entry->value();
    echo "\n";
}
';

// example #4
$examples[] = new Example();
$examples[3]->title = 'Set';
$examples[3]->sourceCode = 'use \\System\\Collections\\Set;


$s = new Set();
$s->add("PZ");
$s->add("TM");
$s->add("MK");
$s->add("TM");


$serialized = serialize($s);
$unserialized = unserialize($serialized);

foreach ($unserialized as $item) {
    echo $item;
    echo "\n";
}
';

require_once './shutdown.inc.php';
