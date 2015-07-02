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


$count = 10;
$seq = \System\Linq\Enumerable::buildWhile(function($ctx) use ($count) {
                                               $ctx->newItem = $ctx->index + 1;
                                               $ctx->addItem = 0 == $ctx->newItem % 2;

                                               return $ctx->index < $count;
                                           });

$arr1 = array(1, 2, 3, 4, 5);
array_splice($arr1, 1, 2);

foreach ($arr1 as $key => $item) {
//    echo "{$key} => {$item}<br />";
}

$a = new \DateTime('1979-09-23');
$b = new \DateTime('1986-12-22');

echo var_export($a < $b);
