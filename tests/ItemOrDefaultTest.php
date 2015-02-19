<?php

require_once 'common_include.php';


use \System\Linq\Enumerable as Enumerable;


class ItemOrDefaultTest extends TestCaseBase {
    /**
     * \System\Collections\Generic\IEnumerable::firstOrDefault()
     */
    public function testFirstOrDefault() {
        $r1 = self::createRangeArray(1, 5);
        $r2 = Enumerable::createEmpty();
        
        $this->assertEquals(1, $r1->firstOrDefault()); 
        $this->assertEquals('TM', $r2->firstOrDefault('TM'));
        
        $r1->reset();
        $r2->reset();
        $this->assertEquals(2, $r1->firstOrDefault(function($i) {
            return $i > 1;
        }));
        $this->assertEquals('TM+MK', $r2->firstOrDefault(function($i) {
            return true;
        }, 'TM+MK'));
    }
    
    /**
     * \System\Collections\Generic\IEnumerable::lastOrDefault()
     */
    public function testLastOrDefault() {
        $r1 = self::createRangeArray(1, 5979);
        $r2 = Enumerable::createEmpty();
        
        $this->assertEquals(5979, $r1->lastOrDefault()); 
        $this->assertEquals('MK', $r2->lastOrDefault('MK'));
        
        $r1->reset();
        $r2->reset();
        $this->assertEquals(5978, $r1->lastOrDefault(function($i) {
            return $i < 5979;
        }));
        $this->assertEquals('MK+TM', $r2->lastOrDefault(function($i) {
            return true;
        }, 'MK+TM'));
    }
    
    /**
     * \System\Collections\Generic\IEnumerable::singleOrDefault()
     */
    public function testSingleOrDefault() {
        $seq1 = Enumerable::createEmpty();
        $seq2 = Enumerable::fromValues(5979);
        $seq3 = Enumerable::fromValues(5979, 23979);
        $seq4 = Enumerable::fromValues(11, 22);
        $seq5 = Enumerable::fromValues(1, 2);
        $seq6 = Enumerable::fromValues(1, 2);
        
        $this->assertEquals('TM', $seq1->singleOrDefault('TM'));
        $this->assertEquals(5979, $seq2->singleOrDefault('TM'));
        
        $failed3 = false;
        try {
            $res3 = $seq3->singleOrDefault('TM');
        }
        catch (\Exception $e) {
            $failed3 = true;
        }
        $this->assertTrue($failed3);
        
        $this->assertEquals(22, $seq4->singleOrDefault(function($i) {
                                                           return $i > 20;
                                                       }, 'TM'));
        $this->assertEquals(null, $seq4->singleOrDefault(function($i) {
            return $i > 2;
        }));
        
        $failed6 = false;
        try {
            $res6 = $this->assertEquals(null, $seq6->singleOrDefault(function($i) {
                return $i > 0;
            }));
        }
        catch (\Exception $e) {
            $failed6 = true;
        }
        $this->assertTrue($failed6);
    }
}
