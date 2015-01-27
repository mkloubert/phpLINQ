<?php

require_once 'common_include.php';


use \System\Linq\Enumerable as Enumerable;

abstract class TestCaseBase extends PHPUnit_Framework_TestCase {
    protected static function createRangeArray($start = 0, $count = 10) {
        return Enumerable::fromArray(Enumerable::range($start, $count)
                                               ->toArray());
    }
}
