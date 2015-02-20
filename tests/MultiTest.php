<?php

require_once 'common_include.php';

use \System\Linq\Enumerable as Enumerable;


class MultiTest extends TestCaseBase {
    public function multiTest1() {
        $seq1 = Enumerable::fromValues('30', '10', '50', '20', '10', '40')
                          ->select(function($x) { return intval($x) / 10; })
                          ->where(function($x) { return $x % 2 == 1; })
                          ->select(function($x) { return floatval($x); })
                          ->distinct();
        
        $a1 = $seq1->toArray();
        
        $diff1 = array_diff($a1, array(3.0, 1.0, 5.0));
        $this->assertTrue(empty($diff1));
    }
}
