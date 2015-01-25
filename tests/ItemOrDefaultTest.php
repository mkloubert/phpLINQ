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
}
