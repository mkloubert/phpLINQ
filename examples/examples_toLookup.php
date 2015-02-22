<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'toLookup()';

// example #1
$examples[] = new Example();
$examples[0]->title = 'Default behavior';
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

class Person {
    public function __construct($name) {
        $this->Name = $name;
    }

    public $Name;
}

class Pet {
    public function __construct($name, Person $owner) {
        $this->Name = $name;
        $this->Owner = $owner;
    }

    public $Name;
    public $Owner;
}

$persons = array(new Person("Tanja"),
                 new Person("Marcel"),
                 new Person("Yvonne"),
                 new Person("Josefine"));

$pets = array(new Pet("Gina"     , $persons[1]),
              new Pet("Schnuffi" , $persons[1]),
              new Pet("Schnuffel", $persons[2]),
              new Pet("WauWau"   , $persons[0]),
              new Pet("Lulu"     , $persons[3]),
              new Pet("Sparky"   , $persons[0]),
              new Pet("Asta"     , $persons[1]));

$petSeq = Enumerable::fromArray($pets);

$lu = $petSeq->toLookup(function($orgKey, $pet) {
                            return $pet->Owner->Name;
                        });

foreach ($lu as $grp) {
    // IGrouping
    echo $grp->key() . "\n";

    // enumerate items of current group
    foreach ($grp as $item) {
        echo  "\t" . $item->Name . "\n";
    }
}
';

// example #2
$examples[] = new Example();
$examples[1]->title = 'Custom key selector';
$examples[1]->sourceCode = 'use \\System\\Linq\\Enumerable;

$myKeySelector = function($orgKey, $item) {
    return "key::" . trim($orgKey);
};
        
$seq = Enumerable::fromValues(1, 2, 3, 4, 5);

$dict = $seq->toDictionary($myKeySelector);
        
foreach ($dict->keys() as $key) {
    echo "[{$key}] = " . var_export($dict[$key], true) . "\n";
}
';

// example #3
$examples[] = new Example();
$examples[2]->title = 'Custom key comparer';
$examples[2]->sourceCode = 'use \\System\\Linq\\Enumerable;

$myKeyComparer = function($x, $y) {
    return $x === $y;
};

$seq = Enumerable::fromValues(1, 2, 3, 4, 5);

$dict      = $seq->toDictionary(null, $myKeyComparer);
$dict[1]   = "TM";
$dict[4.0] = "1979-09-05";
        
foreach ($dict->keys() as $key) {
    echo "[{$key}] = " . var_export($dict[$key], true) . "\n";
}
';


require_once './shutdown.inc.php';
