<?php

require_once 'common_include.php';


use \System\Linq\Enumerable as Enumerable;


class SkipAndTakeTest extends TestCaseBase {
    /**
     * \System\Collections\Generic\IEnumerable::skip()
     */
    public function testSkip() {
        $seq = Enumerable::fromArray(array(239, 5979, 22));
        
        $a = array(5979, 22);
        
        $diff = array_diff($a, $seq->skip(1)
                                   ->toArray());
        
        $this->assertTrue(empty($diff));
    }
    
    /**
     * \System\Collections\Generic\IEnumerable::take()
     */
    public function testTake() {
        $seq = Enumerable::fromArray(array(239, 5979, 22));
        
        $a = array(239);
        
        $diff = array_diff($a, $seq->take(1)
                                   ->toArray());
        
        $this->assertTrue(empty($diff));
    }
}
