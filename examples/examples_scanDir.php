<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'scanDir()';

// example #1
$examples[] = new Example();
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;

$dirsAndFiles = Enumerable::scanDir(__DIR__);

echo "Scanning directory \'" . __DIR__ . "\'...\n\n";
foreach ($dirsAndFiles as $item) {
    echo "[{$item->type}] {$item->name}\n";
}
';

// example #2
$examples[] = new Example();
$examples[1]->title = 'Grouped';
$examples[1]->sourceCode = 'use \\System\\Linq\\Enumerable;

// ILookup object
$dirsAndFiles = Enumerable::scanDir(__DIR__, true);

echo "Scanning directory \'" . __DIR__ . "\'...\n\n";
foreach ($dirsAndFiles as $grp) {
    // IGrouping
    echo "[{$grp->key()}]:\n";

    foreach ($grp as $item) {
        echo "\t{$item->name}\n";
    }
}
';

require_once './shutdown.inc.php';
