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
