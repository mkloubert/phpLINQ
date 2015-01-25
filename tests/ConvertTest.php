<?php

require_once 'common_include.php';


use \System\Linq\Enumerable as Enumerable;


class ConvertTest extends TestCaseBase {
	/**
	 * \System\Collections\Generic\IEnumerable::toArray()
	 */
	public function testToArray() {
		$r = Enumerable::fromValues(1, 2, 3, 4);
		
		$a1 = $r->toArray();
		$this->assertTrue(is_array($a1));
		$this->assertFalse(is_array($r));
		
		$a2 = array(1, 2, 3, 4);
		
		$a3 = array_diff($a1, $a2);
		$this->assertTrue(empty($a3));
		
		$i = 0;
		foreach ($a1 as $k => $v) {
			$this->assertEquals($k, $i++);
		}
	}
	
	/**
	 * \System\Collections\Generic\IEnumerable::toDictionary()
	 */
	public function testToDictionary() {
		$r = Enumerable::fromValues(1, 2, 3, 4);
		
		$d1 = $r->toDictionary();
		$this->assertTrue(is_array($d1));
		$this->assertFalse(is_array($r));
		
		$d2 = array(1, 2, 3, 4);
		
		$d3 = array_diff($d1, $d2);
		$this->assertTrue(empty($d3));
		
		$i = 0;
		foreach ($d1 as $k => $v) {
			$this->assertEquals($k, $i++);
		}
		
		$r->reset();
		$d4 = $r->toDictionary(function($key, $value) {
			return sprintf('TM_%s_%s', $key
					                 , $value);
		});
		
		$this->assertTrue(is_array($d4));
			
		$i = 0;
		foreach ($d4 as $k => $v) {
			$this->assertEquals($k,
					            sprintf('TM_%s_%s', $i++
					            		          , $v));
		}
	}
}
