<?php

require_once 'common_include.php';


use \System\Linq\Enumerable as Enumerable;


class ScalarTest extends TestCaseBase {
    /**
     * \System\Collections\Generic\IEnumerable::average()
     */
    public function testAverage() {
        $r1 = self::createRangeArray(1, 5);
        $r2 = Enumerable::createEmpty();
        
        $this->assertEquals(3, $r1->average());
        $this->assertEquals('TM', $r2->average('TM'));
    }
    
    /**
     * \System\Collections\Generic\IEnumerable::count()
     */
    public function testCount() {
        $r1 = self::createRangeArray(1, 5);
        $r2 = Enumerable::createEmpty();
    
        $this->assertEquals(5, $r1->count());
        $this->assertEquals(0, $r2->count());
        
        // count()
        $r1->reset();
        $this->assertEquals(5, count($r1));
        $r2->reset();
        $this->assertEquals(0, count($r2));
    }
    
    /**
     * \System\Collections\Generic\IEnumerable::max()
     */
    public function testMax() {
        $r1 = self::createRangeArray();    // 0 - 9
        $r2 = Enumerable::createEmpty();

        $this->assertEquals(9, $r1->max());
        $this->assertEquals('1979-09-05', $r2->max('1979-09-05'));
    }
    
    /**
     * \System\Collections\Generic\IEnumerable::min()
     */
    public function testMin() {
        $r1 = self::createRangeArray(-1);    // -1 - 8
        $r2 = Enumerable::createEmpty();
        
        $this->assertEquals(-1, $r1->min());
        $this->assertEquals('MK', $r2->average('MK'));
    }
    
    /**
     * \System\Collections\Generic\IEnumerable::multiply()
     */
    public function testMultiply() {
        $r1 = self::createRangeArray(1, 6);    // 1 - 6
        $r2 = Enumerable::createEmpty();
    
        $this->assertEquals(720, $r1->multiply());
        $this->assertEquals('1979-09-23', $r2->average('1979-09-23'));
    }
    
    /**
     * \System\Collections\Generic\IEnumerable::sum()
     */
    public function testSum() {
        $r1 = self::createRangeArray(1, 4);    // 1 - 4
        $r2 = Enumerable::createEmpty();
    
        $this->assertEquals(10, $r1->sum());
        $this->assertEquals('1981-07-01', $r2->sum('1981-07-01'));
    }
}
