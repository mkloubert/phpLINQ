<?php

/**********************************************************************************************************************
 * phpLINQ (https://github.com/mkloubert/phpLINQ)                                                                     *
 *                                                                                                                    *
 * Copyright (c) 2015, Marcel Joachim Kloubert <marcel.kloubert@gmx.net>                                              *
 * All rights reserved.                                                                                               *
 *                                                                                                                    *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the   *
 * following conditions are met:                                                                                      *
 *                                                                                                                    *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the          *
 *    following disclaimer.                                                                                           *
 *                                                                                                                    *
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the       *
 *    following disclaimer in the documentation and/or other materials provided with the distribution.                *
 *                                                                                                                    *
 * 3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote    *
 *    products derived from this software without specific prior written permission.                                  *
 *                                                                                                                    *
 *                                                                                                                    *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, *
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE  *
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, *
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR    *
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,  *
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE   *
 * USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.                                           *
 *                                                                                                                    *
 **********************************************************************************************************************/

chdir(__DIR__);


spl_autoload_register(function($clsName) {
    $classFile = false;

    $classDir = false;
    if (0 === stripos($clsName, "System\\")) {
        $classDir = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
    }
    else if ((0 === stripos($clsName, "phpLINQ\\")) ||
             (0 === stripos($clsName, "phpDocumentor\\"))) {

        $classDir = realpath(__DIR__ . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR);
    }

    if (false !== $classDir) {
        $classFile = realpath($classDir . DIRECTORY_SEPARATOR .
                              str_replace("\\", DIRECTORY_SEPARATOR, $clsName) .
                              '.php');
    }

    if (false !== $classFile) {
        require_once $classFile;
    }
});


$docFile = null;
$docFileFound = null;
$docXml = null;
$outDir = './out';


$docFile = trim($docFile);
if ('' !== $docFile) {
    $docFile = realpath($docFile);
    if (false === $docFile) {
        $docFileNotFound = true;
    }
}
else {
    $docFile = realpath('./docs.xml');
    if (false !== $docFile) {
        $docFileNotFound = false;
    }
}

if (true === $docFileFound) {
    exit(1);
}

if (false === $docFileNotFound) {
    $docXml = simplexml_load_file($docFile);
}

if (false === $docXml) {
    exit(2);
}

if (!file_exists($outDir)) {
    if (!mkdir($outDir, 0777, true)) {
        exit(3);
    }
}

$outDir = realpath($outDir);
if (!is_dir($outDir)) {
    exit(4);
}

$proj = \phpLINQ\Docs\Project::fromXml($docXml);
$proj->baseDir(__DIR__);
$proj->outDir($outDir);

$proj->generate();
