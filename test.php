<?php

/**
 *  LINQ concept for PHP
 *  Copyright (C) 2015  Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 *
 *    This library is free software; you can redistribute it and/or
 *    modify it under the terms of the GNU Lesser General Public
 *    License as published by the Free Software Foundation; either
 *    version 3.0 of the License, or (at your option) any later version.
 *
 *    This library is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 *    Lesser General Public License for more details.
 *
 *    You should have received a copy of the GNU Lesser General Public
 *    License along with this library.
 */


/**
 * Autoloader.
 *
 * @param string $clsName Name of the class to load.
 *
 * @author Marcel Kloubert <marcel.kloubert@gmx.net>
 */
spl_autoload_register(function($clsName) {
    $file = realpath(__DIR__ . DIRECTORY_SEPARATOR .
                     str_replace('\\', DIRECTORY_SEPARATOR, $clsName) .
                     '.php');

    if (false !== $file) {
        require_once $file;
    }
});


$seq = \System\Linq\Enumerable::fromValues(5979, 23979, null, 23979, 1781, 241279);

$newSeq = $seq->select(function($item) {
                            return strval($item);
                        })    // transform all values
                              // to string
              ->where(function($item) {
                         return !empty($item);
                })    // filter out all values that are empty
                ->skip(1)    // skip the first element ('5979')
                ->take(3)    // take the next 3 elements from current position
                             // ('23979', '23979' and '1781')
                ->distinct()    // remove duplicates
                ->order()    // sort
                ->reverse();

foreach ($newSeq as $item) {
    // echo "{$item}<br />";
}

$dict    = new \System\Collections\Dictionary();
$dict['PZ'] = 19861222;
$dict['MK'] = 19790923;

foreach ($dict as $entry) {
    // echo "{$entry->key()} => {$entry->value()}";
}

$list = new \System\Collections\Collection();
$list[] = 1;
$list[] = 3;

foreach ($list as $item) {
    // echo "{$item}";
}

$dict2 = new \System\Collections\Dictionary(function (\stdClass $x, \stdClass $y) {
                                                return $x->key == $y->key;
                                            });

$key1      = new \stdClass();
$key1->key = 1;

$key2      = new \stdClass();
$key2->key = 2;

$key3      = new \stdClass();
$key3->key = '1';

$dict2[$key1] = 1000;
$dict2[$key2] = 2000;
$dict2[$key3] = 3000;

foreach ($dict2 as $item) {
    echo "{$item->value()}<br />";
}
