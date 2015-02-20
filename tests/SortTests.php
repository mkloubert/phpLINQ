<?php

require_once 'common_include.php';

use \System\Linq\Enumerable as Enumerable;


class SortTests extends TestCaseBase {
    /**
     * \System\Collections\Generic\IEnumerable::order()
     */
    public function testOrder() {
        $seq1 = Enumerable::fromValues(2, 3, 1);
    
        $a = $seq1->order()
                  ->toArray();
    
        $this->assertEquals($a[0], 1);
        $this->assertEquals($a[1], 2);
        $this->assertEquals($a[2], 3);
    }
    
    /**
     * \System\Collections\Generic\IEnumerable::orderBy()
     */
    public function testOrderBy() {
        $seq1 = Enumerable::fromValues(2, 3, 1);
        
        $a = $seq1->orderBy(function($x) { return $x; })
                  ->toArray();
        
        $this->assertEquals($a[0], 1);
        $this->assertEquals($a[1], 2);
        $this->assertEquals($a[2], 3);
    }
    
    /**
     * \System\Collections\Generic\IEnumerable::orderByDescending()
     */
    public function testOrderByDescending() {
        $seq1 = Enumerable::fromValues(2, 3, 1);
        
        $a = $seq1->orderByDescending(function($x) { return $x; })
                  ->toArray();
        
        $this->assertEquals($a[0], 3);
        $this->assertEquals($a[1], 2);
        $this->assertEquals($a[2], 1);
    }
    
    /**
     * \System\Collections\Generic\IEnumerable::orderDescending()
     */
    public function testOrderDescending() {
        $seq1 = Enumerable::fromValues(2, 3, 1);
    
        $a = $seq1->orderDescending()
                  ->toArray();
    
        $this->assertEquals($a[0], 3);
        $this->assertEquals($a[1], 2);
        $this->assertEquals($a[2], 1);
    }
    
    /**
     * \System\Collections\Generic\IEnumerable::reverse()
     */
    public function testReverse() {
    	$seq1 = Enumerable::fromValues(1, 2, 3, 2, 7);
    	
    	$a = $seq1->reverse()
    	          ->toArray();
    	
    	$this->assertEquals($a[0], 7);
    	$this->assertEquals($a[1], 2);
    	$this->assertEquals($a[2], 3);
    	$this->assertEquals($a[3], 2);
    	$this->assertEquals($a[4], 1);
    }
}
