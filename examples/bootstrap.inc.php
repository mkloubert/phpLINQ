<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


function __autoload($clsName) {
	$classFilePrefix = '..' . 
			           DIRECTORY_SEPARATOR .
                       str_replace("\\", DIRECTORY_SEPARATOR, $clsName);
	
	$classFile = $classFilePrefix . '.php';
	if (!isPHP_5_5()) {
		// pre PHP 5.5
		
		$classFilePHP53 = $classFilePrefix . '.PHP5.3.php';
		if (file_exists($classFilePHP53)) {
			// use specific "pre file"
			$classFile = $classFilePHP53;
		}
	}
	
    require_once $classFile;    
}

function isPHP_5_5() {
	return version_compare(PHP_VERSION, '5.5.0', '>=');
}

function parseForHtmlOutput($str) {
    $str = strval($str);
    
    $str = str_ireplace("\t", '    '  , $str);
    $str = str_ireplace("\r", ''      , $str);
    
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
