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
     * \System\Collections\Generic\IEnumerable::where()
     */
    public function testWhere() {
        $seq1 = Enumerable::fromValues(1, 2, 3, 4, 5);
        $seq2 = $seq1->where(function($i) {
            return $i % 2 == 0;
        });
        
        $this->assertEquals(2, count($seq2));
    }
}
