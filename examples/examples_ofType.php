<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'cast()';

// example #1
$examples[] = new Example();
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

class MyClass {
    public function __toString() {
        return "MyClass";
    }
}

$showSeqence = function($seq) {
    foreach ($seq as $item) {
        echo sprintf("[%s] %s", gettype($item)
                              , $item);
        echo "\n";
    }
};

// sequence of integers
$seq = Enumerable::fromValues(1, "2", 3, new MyClass());
        
echo "integers:\n";
$showSeqence($seq->ofType("integer"));

echo "\n";

// cast to strings
echo "MyClass:\n";
$showSeqence($seq->reset()
                 ->ofType("MyClass"));
';

require_once './shutdown.inc.php';
