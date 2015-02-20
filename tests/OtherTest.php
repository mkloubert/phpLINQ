<?php

require_once 'common_include.php';


use \System\Linq\Enumerable as Enumerable;


class OtherTest extends TestCaseBase {
    /**
     * \System\Collections\Generic\IEnumerable::concat()
     */
    public function testConcat() {
        $seq1 = Enumerable::fromValues(1, 2, 3, 4, 5);
        $seq2 = Enumerable::fromValues(11, 22, 33, 44, 55);
        $seq3 = $seq1->concat($seq2);
        
        $this->assertEquals(10, count($seq3));
    }

    /**
     * \System\Collections\Generic\IEnumerable::defaultIfEmpty()
     */
    public function testDefaultIfEmpty() {
        $seq1 = Enumerable::fromValues(5979, 23979);
        $seq2 = Enumerable::createEmpty();
    
        $a1 = $seq1->defaultIfEmpty('TM', 'MK')->toArray();
        $a2 = $seq2->defaultIfEmpty('TM', 'MK')->toArray();
    
        $diff1 = array_diff($a1, array(5979, 23979));
        $this->assertTrue(empty($diff1));
    
        $diff2 = array_diff($seq2->toArray(), array('TM', 'MK'));
        $this->assertTrue(empty($diff2));
    }
    
    /**
     * \System\Collections\Generic\IEnumerable::distinct()
     */
    public function testDistinct() {
        $seq1 = Enumerable::fromValues(1, 1, 2, 2, 3, 5, 4, 5);
        $seq2 = Enumerable::fromValues(1, 2, 3, '1', 4, 5);
        
        $a1 = $seq1->distinct()->toArray();
        $a2 = $seq2->distinct(function($x, $y) {
            return gettype($x) == gettype($y);
        })->toArray();
        
        $diff1 = array_diff($a1, array(1, 2, 3, 5, 4));
        $this->assertTrue(empty($diff1));
        
        $diff2 = array_diff($a2, array(1, '1'));
        $this->assertTrue(empty($diff2));
    }
    
    /**
     * \System\Collections\Generic\IEnumerable::except()
     */
    public function testExcept() {
    	$seq = Enumerable::fromValues(1, 2, 3, 4, 5);
    	$exc = Enumerable::fromValues(4, 1);
    	
    	$a = $seq->except($exc)
    	         ->toArray();
    	
    	$this->assertEquals(2, $a[0]);
    	$this->assertEquals(3, $a[1]);
    	$this->assertEquals(5, $a[2]);
    }
    
    /**
     * \System\Collections\Generic\IEnumerable::intersect()
     */
    public function testIntersect() {
    	$seq1 = Enumerable::fromValues(1, 2, 3, 4, 5);
    	$seq2 = Enumerable::fromValues(4, 1);
    	 
    	$a = $seq1->intersect($seq2)
    	          ->toArray();
    	 
    	$this->assertEquals(1, $a[0]);
    	$this->assertEquals(4, $a[1]);
    }
    
    /**
     * \System\Collections\Generic\IEnumerable::sequenceEqual()
     */
    public function testSequenceEqual() {
    	$seq1_L = Enumerable::fromValues(1, 2, 3);
    	$seq1_R = Enumerable::fromValues(1, 2, 3);
    	
    	$seq2_L = Enumerable::fromValues(1, 2, 3);
    	$seq2_R = Enumerable::fromValues(1, 2);
    	
    	$seq3_L = Enumerable::fromValues(1, 2);
    	$seq3_R = Enumerable::fromValues(1, 2, 3);
    	
    	$seq4_L = Enumerable::fromValues();
    	$seq4_R = Enumerable::fromValues(1, 2, 3);
    	
    	$seq5_L = Enumerable::fromValues();
    	$seq5_R = Enumerable::fromValues(1, 2, 3);
    	
    	$seq6_L = Enumerable::fromValues(1, 2, 3);
    	$seq6_R = Enumerable::fromValues(10, 20, 30);
    	
    	$this->assertTrue($seq1_L->sequenceEqual($seq1_R));
    	$this->assertFalse($seq2_L->sequenceEqual($seq2_R));
    	$this->assertFalse($seq3_L->sequenceEqual($seq3_R));
    	$this->assertFalse($seq4_L->sequenceEqual($seq4_R));
    	$this->assertFalse($seq5_L->sequenceEqual($seq5_R));
    	$this->assertTrue($seq6_L->sequenceEqual($seq6_R, function($x, $y) {
    		return $x == $y / 10;
    	}));
    }
    
    /**
     * \System\Collections\Generic\IEnumerable::union()
     */
    public function testUnion() {
    	$seq1 = Enumerable::fromValues(5, 3, 9, 7, 5, 9, 3, 7);
    	$seq2 = Enumerable::fromValues(8, 3, 6, 4, 4, 9, 1, 0);
    
    	$a = $seq1->union($seq2)
    	          ->toArray();
    
    	$this->assertEquals(5, $a[0]);
    	$this->assertEquals(3, $a[1]);
    	$this->assertEquals(9, $a[2]);
    	$this->assertEquals(7, $a[3]);
    	$this->assertEquals(8, $a[4]);
    	$this->assertEquals(6, $a[5]);
    	$this->assertEquals(4, $a[6]);
    	$this->assertEquals(1, $a[7]);
    	$this->assertEquals(0, $a[8]);
    }
    
    /**
     * \System\Collections\Generic\IEnumerable::where()
     */
    public function testWhere() {
        $seq1 = Enumerable::fromValues(1, 2, 3, 4, 5);
        $seq2 = $seq1->where(function($i) {
            return $i % 2 == 0;
        });
        
        $this->assertEquals(2, count($seq2));
    }
    
    /**
     * \System\Collections\Generic\IEnumerable::zip()
     */
    public function testZip() {
    	$seq1 = Enumerable::fromValues(1, 2, 3, 4);
    	$seq2 = Enumerable::fromValues('one', 'two', 'three');
    
    	$a = $seq1->zip($seq2, function($x, $y) {
    		                       return sprintf('%s %s', $x, $y);
    	                       })
    	          ->toArray();
    
    	$this->assertEquals(3, count($a));
    	
    	$this->assertEquals('1 one'  , $a[0]);
    	$this->assertEquals('2 two'  , $a[1]);
    	$this->assertEquals('3 three', $a[2]);
    }
}
