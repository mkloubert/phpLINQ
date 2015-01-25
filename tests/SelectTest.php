<?php

require_once 'common_include.php';


use \System\Linq\Enumerable as Enumerable;


class SelectTest extends TestCaseBase {
    /**
     * \System\Collections\Generic\IEnumerable::select()
     */
    public function testSelect() {
        $seq1 = Enumerable::fromValues(1, 2, 3);
        $seq2 = $seq1->select(function($i) {
            return strval($i);
        });
        
        foreach ($seq2 as $i) {
            $this->assertTrue(gettype($i) == 'string');
        }
    }
    
    /**
     * \System\Collections\Generic\IEnumerable::selectMany()
     */
    public function testSelectMany() {
        $seq1 = Enumerable::fromValues(1, 2, 3);
        $seq2 = $seq1->selectMany(function($i) {
            return array($i, $i * 10, $i * 100);
        });
        
        $seq1->reset();
        
        $this->assertEquals(count($seq2),
                            count($seq1) * 3);
    }
}
