<?php

$includeFile = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR;

set_include_path(get_include_path() . PATH_SEPARATOR . $includeFile);

spl_autoload_register(function($clsName) use ($includeFile) {
    require_once $includeFile . '../' . str_replace('\\', '/', $clsName) . '.php';
});
