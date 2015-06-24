<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


function __autoload($clsName) {
	$classFile = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' .
			              DIRECTORY_SEPARATOR .
                          str_replace("\\", DIRECTORY_SEPARATOR, $clsName) .
                          '.php');

	if (false !== $classFile) {
        require_once $classFile;
    }
}

function isPHP_5_5() {
    return version_compare(PHP_VERSION, '5.5', '>=');
}

function parseForHtmlOutput($str) {
    $str = strval($str);
    
    $str = str_ireplace("\t", '    ', $str);
    $str = str_ireplace("\r", ''    , $str);
    
    return htmlentities($str);
}

final class Example {
    private static $_nextId = 0;
    
    public function __construct() {
        $this->id = self::$_nextId++;
    }
    
    public $description;
    public $id;
    public $sourceCode;
    public $title;
}

$pageTitle = '';
$examples = array();

ob_start();
