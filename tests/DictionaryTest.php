<?php

require_once 'common_include.php';


use \System\Collections\DictionaryEntry as DictionaryEntry;
use \System\Collections\Dictionary as Dictionary;
use \System\Linq\Enumerable as Enumerable;


class DictionaryTest extends TestCaseBase {
    /**
     * @see \System\Collections\IDictionary::add()
     */
    public function testAdd() {
        $d = new Dictionary();

        $this->assertEquals(0, count($d));
        
        $d->add('TM', 5979);
        $this->assertEquals(1, count($d));
        $this->assertTrue($d->containsKey('TM'));
        $this->assertFalse($d->containsKey('MK'));
    }
    
    public function testArrayAccess1() {
        $d = new Dictionary();
        
        $this->assertEquals(0, count($d));
        
        $d['TM'] = 5979;
        $d['MK'] = 23979;
        $this->assertEquals(2, count($d));
        
        $this->assertTrue(isset($d['TM']));
        $this->assertTrue(isset($d['MK']));
        $this->assertFalse(isset($d['mk']));
        
        unset($d['MK']);
        $this->assertTrue(isset($d['TM']));
        $this->assertFalse(isset($d['MK']));
    }
    
    public function testArrayAccess2() {
        $d = new Dictionary(function($x, $y) {
            return trim(strtolower($x)) ==
                   trim(strtolower($y));
        });
    
        $this->assertEquals(0, count($d));
    
        $d['TM'] = 5979;
        $d['MK'] = 23979;
        $this->assertEquals(2, count($d));
    
        $this->assertTrue(isset($d['TM']));
        $this->assertTrue(isset($d['MK']));
        $this->assertTrue(isset($d['mk']));
    
        unset($d['  Mk     ']);
        $this->assertTrue(isset($d['TM']));
        $this->assertFalse(isset($d['MK']));
    }
    
    /**
     * @see \System\Collections\IDictionary::clear()
     */
    public function testClear() {
        $d = new Dictionary();
        
        $this->assertEquals(0, count($d));
        
        $d->add('TM', 5979);
        $d->add('MK', 23979);
        $this->assertEquals(2, count($d));
        
        $d->clear();
        $this->assertEquals(0, count($d));
    }
    
    public function testIterator() {
        $d = new Dictionary();
        
        $d->add('TM', 5979);
        $d->add('MK', 23979);
        
        $i = 0;
        foreach ($d as $entry) {
            if ($entry instanceof DictionaryEntry) {
                ++$i;
            }
        }
        
        $this->assertEquals(2, $i);
    }
    
    /**
     * @see \System\Collections\IDictionary::keys()
     */
    public function testKeys() {
        $d = new Dictionary();
    
        $this->assertEquals(0, count($d->keys()));
    
        $d->add('TM', '1979-09-05');
        $d->add('MK', '1979-09-23');
        $this->assertEquals(2, count($d->keys()));
    
        $d->clear();
        $this->assertEquals(0, count($d->keys()));
    }
    
    /**
     * @see \System\Collections\IDictionary::remove()
     */
    public function testRemove1() {
        $d = new Dictionary();
        
        $this->assertEquals(0, count($d));
        
        $d->add('TM', 0509.1979);
        $d->add('MK', 2309.1979);
        $this->assertEquals(2, count($d));
        
        $d->removeKey('MK');
        $this->assertEquals(1, count($d));
        
        $d->remove('mk');
        $this->assertEquals(1, count($d));
        
        $d->removeKey('TM');
        $this->assertEquals(0, count($d));
    }
    
    /**
     * @see \System\Collections\IDictionary::remove()
     */
    public function testRemove2() {
        $d = new Dictionary(function($x, $y) {
            return trim(strtolower($x)) ==
                   trim(strtolower($y));
        });
    
        $this->assertEquals(0, count($d));
    
        $d->add('TM', '0509,1979');
        $d->add('MK', 2309.1979);
        $this->assertEquals(2, count($d));
    
        $d->removeKey('mK');
        $this->assertEquals(1, count($d));
    
        $this->assertFalse($d->remove('mk'));
    
        $d->removeKey(' TM ');
        $this->assertEquals(0, count($d));
    }
    
    /**
     * @see \System\Collections\IDictionary::values()
     */
    public function testValues() {
        $d = new Dictionary();
    
        $this->assertEquals(0, count($d->values()));
    
        $d->add('TM', '19790905');
        $d->add('MK', '19790923');
        $this->assertEquals(2, count($d->values()));
    
        $d->clear();
        $this->assertEquals(0, count($d->values()));
    }
}
