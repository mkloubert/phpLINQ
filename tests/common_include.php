<?php

$includeFile = realpath(dirname(__FILE__)) . '/';

function __autoload($clsName) {
    require_once $includeFile . '../' . str_replace('\\', '/', $clsName) . '.php';
}
