<?php

require_once 'common_include.php';


use \System\Linq\Enumerable as Enumerable;


class PredicateTest extends TestCaseBase {
    /**
     * \System\Collections\Generic\IEnumerable::all()
     */
    public function testAll() {
        $r1 = self::createRangeArray(1, 5);
        $r2 = Enumerable::createEmpty();
        $r3 = self::createRangeArray(1, 6);
        
        $this->assertTrue($r1->all(function($i) {
            return $i < 6;
        }));
        
        $this->assertTrue($r2->all(function($i) {
            return $i < 6;
        }));
        
        $this->assertFalse($r3->all(function($i) {
            return $i < 6;
        }));
    }
    
    /**
     * \System\Collections\Generic\IEnumerable::any()
     */
    public function testAny() {
        $r1 = self::createRangeArray(1, 5);
        $r2 = Enumerable::createEmpty();
        $r3 = self::createRangeArray(1, 6);
        
        $this->assertTrue($r1->any(function($i) {
            return $i < 6;
        }));
        
        $this->assertFalse($r2->any(function($i) {
            return $i < 6;
        }));
    
        $this->assertFalse($r3->any(function($i) {
            return $i < 1;
        }));
    }
    
    /**
     * \System\Collections\Generic\IEnumerable::contains()
     */
    public function testContains() {
        $r1 = Enumerable::fromValues(0, 1);
        $r2 = Enumerable::fromValues(false);
    
        $this->assertTrue($r1->contains(false));
        $this->assertTrue($r2->contains(false));
        
        $r1->reset();
        $r2->reset();
        $this->assertFalse($r1->contains(false, function($x, $y) {
            return $x === $y;
        }));
        $this->assertTrue($r2->contains(false, function($x, $y) {
            return $x === $y;
        }));
    }
}
