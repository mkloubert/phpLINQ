<?php

require_once 'common_include.php';


use \System\Linq\Enumerable as Enumerable;


class Person {
	public function __construct($name) {
		$this->Name = $name;
	}

	public $Name;
}

class Pet {
	public function __construct($name, Person $owner) {
		$this->Name = $name;
		$this->Owner = $owner;
	}

	public $Name;
	public $Owner;
}

class JoinsAndGroupTest extends TestCaseBase {
	private $_persons;
	private $_pets;
	
	
	public function __construct() {
		$this->_persons = array(new Person("Tanja"),
                                new Person("Marcel"),
                                new Person("Yvonne"),
                                new Person("Josefine"));
		
		$this->_pets = array(new Pet("Gina"     , $this->_persons[1]),
                             new Pet("Schnuffi" , $this->_persons[1]),
                             new Pet("Schnuffel", $this->_persons[2]),
                             new Pet("WauWau"   , $this->_persons[0]),
                             new Pet("Lulu"     , $this->_persons[3]),
                             new Pet("Sparky"   , $this->_persons[0]),
                             new Pet("Asta"     , $this->_persons[1]));
	}
	
	
	/**
	 * \System\Collections\Generic\IEnumerable::groupBy()
	 */
	public function testGroupBy() {
		$pets    = Enumerable::fromArray($this->_pets);
		
		$groups = $pets->groupBy(function($orgKey, $pet) { return $pet->Owner->Name; })
		               ->toArray();
		
		$this->assertEquals('Marcel', $groups[0]->key());
		$this->assertEquals(3       , count($groups[0]));
		
		$this->assertEquals('Yvonne', $groups[1]->key());
		$this->assertEquals(1       , count($groups[1]));
		
		$this->assertEquals('Tanja', $groups[2]->key());
		$this->assertEquals(2      , count($groups[2]));
		
		$this->assertEquals('Josefine', $groups[3]->key());
		$this->assertEquals(1         , count($groups[3]));
	}
	
	/**
	 * \System\Collections\Generic\IEnumerable::groupJoin()
	 */
	public function testGroupJoin() {
		$persons = Enumerable::fromArray($this->_persons);
		$pets    = Enumerable::fromArray($this->_pets);
		
		$joined = $persons->groupJoin($pets,
									  function($orgKey, $person) { return $person->Name; },
									  function($orgKey, $pet) { return $pet->Owner->Name; },
									  function($person, $pets) {
										  return strtoupper(sprintf('%s::%s', $person->Name
											                                , count($pets)));
									  })->toArray();
		
		$this->assertEquals('TANJA::2'   , $joined[0]);
		$this->assertEquals('MARCEL::3'  , $joined[1]);
		$this->assertEquals('YVONNE::1'  , $joined[2]);
		$this->assertEquals('JOSEFINE::1', $joined[3]);
	}
	
	/**
	 * \System\Collections\Generic\IEnumerable::join()
	 */
	public function testJoin() {
		$persons = Enumerable::fromArray($this->_persons);
		$pets    = Enumerable::fromArray($this->_pets);
		
		$joined = $persons->join($pets,
				                 function($orgKey, $person) { return $person->Name; },
				                 function($orgKey, $pet) { return $pet->Owner->Name; },
				                 function($person, $pet) {
				                     return strtolower(sprintf('%s::%s', $person->Name
				                     		                           , $pet->Name));
                                 })->toArray();

		$this->assertEquals('tanja::wauwau'    , $joined[0]);
		$this->assertEquals('tanja::sparky'    , $joined[1]);
		$this->assertEquals('marcel::gina'     , $joined[2]);
		$this->assertEquals('marcel::schnuffi' , $joined[3]);
		$this->assertEquals('marcel::asta'     , $joined[4]);
		$this->assertEquals('yvonne::schnuffel', $joined[5]);
		$this->assertEquals('josefine::lulu'   , $joined[6]);
	}
	
	/**
	 * \System\Collections\Generic\IEnumerable::toLookup()
	 */
	public function testToLookup() {
		$pets = Enumerable::fromArray($this->_pets);
		
		$lu = $pets->toLookup(function($orgKey, $pet) { return $pet->Owner->Name; });
		
		$this->assertEquals(4, count($lu));

		$this->assertTrue($lu->containsKey('Tanja'));
		$this->assertEquals(2, count($lu['Tanja']));
		
		$this->assertTrue(isset($lu['Marcel']));
		$this->assertEquals(3, $lu['Marcel']->count());
		
		$this->assertTrue($lu->offsetExists('Yvonne'));
		$this->assertEquals(1, count($lu['Yvonne']));
		
		$this->assertTrue(isset($lu['Josefine']));
		$this->assertEquals(1, $lu['Josefine']->count());
	}
}
