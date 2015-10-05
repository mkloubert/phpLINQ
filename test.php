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


error_reporting(E_ALL);


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

class TestClass {
    public function __invoke() {

    }
}


use \System\IO\DirectoryInfo;
use \System\IO\FileSystemInfo;
use \System\IO\IFileSystemInfo;
use \System\IO\IFileInfo;

$dir = new DirectoryInfo('.');

// var_dump((string)$dir);

foreach ($dir->enumerateDirectories() as $file) {
    /* @var IFileInfo $file */

    var_dump((string)$file->fullName());
}

// var_dump(pathinfo('.'));

// var_dump((string)$dir->directory());
// var_dump((string)$dir->directory()->directory());
// var_dump((string)$dir->directory()->directory());


// var_dump((string)$dir->directory()->directory()->directory()->directory()->name());
