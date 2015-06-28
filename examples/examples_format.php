<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'format()';

// example #1
$examples[] = new Example();
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

// {0} = 1
// {1} = 2
// {2} = 3
$seq = Enumerable::fromValues(1, 2, 3);

echo var_export($seq->format("{2} = {0} + {1}"),
                true);
';


require_once './shutdown.inc.php';
