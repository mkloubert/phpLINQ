<html>
  <head>
  </head>
  
  <body>
    <pre>
<?php

error_reporting(E_ALL);

require_once './System/Collections/Generic/IEnumerable.php';
require_once './System/Collections/Generic/EnumerableBase.php';
require_once './System/Linq/Enumerable.php';

use \System\Linq\Enumerable as Enumerable;


final class Assert {
    private function __construct() {
        
    }
    
    
    public static function arraysEqual($cat, array $a1, array $a2) {
    	$throwEx = false;
    	
    	if (count($a1) == count($a2)) {
    		for ($i = 0; $i < count($a1); $i++) {
    			if ($a1[$i] != $a2[$i]) {
    				// different content
    				
    				$throwEx = true;
    				break;
    			}
    		}
    	}
    	else {
    		// different count
    		$throwEx = true;
    	}
    	
    	if ($throwEx) {
    		self::throwException($cat, 'Different arrays!');
    	}
    }
    
    public static function areEqual($cat, $left, $right) {
        if ($left != $right) {
            self::throwException($cat, sprintf('Expected: %s but got %s', $right, $left));
        }
    }
    
    public static function isFalse($cat, $predicate) {
        if (false !== $predicate) {
            self::throwException($cat, 'Expected (false)');
        }
    }
    
    public static function isTrue($cat, $predicate) {
        if (true !== $predicate) {
            self::throwException($cat, 'Expected (true)');
        }
    }
    
    private static function throwException($cat, $msg) {
        throw new \Exception(sprintf('[%s] => %s', $cat, $msg));
    }
}

final class LinqTests {
    private static function createRangeArray($start = 0, $count = 10) {
        return Enumerable::fromArray(Enumerable::range($start, $count)
                                               ->toArray());
    }
    
    public function test_All() {
        $r = self::createRangeArray();
    
        $r->rewind();
        Assert::isTrue('Range::all::1', $r->all(function($i) { 
                                                    return $i != 10;
                                                }));
        
        $r->rewind();
        Assert::isFalse('Range::all::2', $r->all(function($i) {
                                                     return $i != 9;
                                                 }));
    }
    
    public function test_Any() {
        $r = self::createRangeArray();
        
        $r->rewind();
        Assert::isTrue('Range::any::1', $r->any());
        
        $r->rewind();
        Assert::isTrue('Range::any::2', $r->any(function($i) { 
                                                    return $i > 7;
                                                }));
        
        $r->rewind();
        Assert::isFalse('Range::any::3', $r->any(function($i) {
                                                     return $i > 9;
                                                 }));
    }
    
    public function test_Concat() {
    	$r1 = self::createRangeArray();    // 0 - 9
    	$r2 = self::createRangeArray(10, 9);    // 10 - 18
    
    	Assert::areEqual('Range::concat', $r1->concat($r2)
    	                                     ->count()
                                        , 19);
    }
    
    public function test_Count() {
        $r = self::createRangeArray();

        Assert::areEqual('Range::count', count($r), 10);
    }
    
    public function test_FirstOrDefault() {
        $r1 = self::createRangeArray();
        $r2 = Enumerable::createEmpty();
        
        $r1->rewind();
        Assert::areEqual('Range::firstOrDefault::1',
                         $r1->firstOrDefault(function($i) {
                             return $i > 3;
                         }),
                         4);
        
        $r1->rewind();
        Assert::areEqual('Range::firstOrDefault::2',
                         $r1->firstOrDefault(function($i) {
                             return $i > 9;
                         }, 666),
                         666);
        
        $r2->rewind();
        Assert::areEqual('Range::firstOrDefault::3',
                         $r2->firstOrDefault(null, 667),
                         667);
    }
    
    public function test_LastOrDefault() {
        $r1 = self::createRangeArray();
        $r2 = Enumerable::createEmpty();
    
        $r1->rewind();
        Assert::areEqual('Range::lastOrDefault::1',
                         $r1->lastOrDefault(function($i) {
                             return $i < 6;
                         }),
                         5);
    
        $r1->rewind();
        Assert::areEqual('Range::lastOrDefault::2',
                         $r1->lastOrDefault(function($i) {
                             return $i > 9;
                         }, 666),
                         666);
    
        $r2->rewind();
        Assert::areEqual('Range::lastOrDefault::3',
                         $r2->lastOrDefault(null, 667),
                         667);
    }
    
    public function test_Max() {
        $r1 = self::createRangeArray();    // 0 - 9
        $r2 = self::createRangeArray(10);    // 10 - 19

        Assert::isTrue('Range::max', 19 == $r1->concat($r2)
                                              ->max());
    }
    
    public function test_Min() {
        $r1 = self::createRangeArray(-1);    // -1 - 8
        $r2 = self::createRangeArray(10);    // 10 - 19
        
        Assert::isTrue('Range::min', -1 == $r1->concat($r2)
                                              ->min());
    }
    
    public function test_SelectMany() {
    	$r1 = self::createRangeArray(1, 3);
    	$r2 = $r1->selectMany(function($i) {
    		return array($i, $i, $i);
    	});
    
    	Assert::arraysEqual('Range::selectMany',
    	                    $r2->toArray(),
    	                    array(1, 1, 1, 2, 2, 2, 3, 3, 3));
    }
    
    public function test_Skip() {
        $r = self::createRangeArray();    // 0 - 10
        
        $r->rewind();
        Assert::areEqual('Range::skip::1', $r->skip(1)->count(), 9);
        
        $r->rewind();
        Assert::areEqual('Range::skip::2', $r->skip(10)->count(), 0);
        
        $r->rewind();
        Assert::areEqual('Range::skip::3', $r->skip(12)->count(), 0);
    }
    
    public function test_Take() {
        $r = self::createRangeArray();    // 0 - 10
        
        $r->rewind();
        Assert::areEqual('Range::take::1', $r->take(2)->count(), 2);
        
        $r->rewind();
        Assert::areEqual('Range::take::2', $r->take(12)->count(), 10);
    }
    
    public function test_ToArray() {
        $r = self::createRangeArray();    // 0 - 10
        $a = $r->toArray();
    
        $r->rewind();
        Assert::isTrue('Range::toArray::1', is_array($a));
    
        $r->rewind();
        Assert::areEqual('Range::take::2', $r->count(), count($a));
    }
    
    public function test_ToDictionary() {
        $r = self::createRangeArray();    // 0 - 10
        $a = $r->toDictionary(function($key, $item) {
            return $key * 2;
        });
    
        $r->rewind();
        foreach ($r as $rk => $ri) {
            Assert::isTrue('Range::toDictionary',
                           isset($a[$rk * 2]));
        }
    }
    
    public function test_Where() {
        $r1 = self::createRangeArray();    // 0-9
        $r2 = self::createRangeArray(9);    // 9-18
    
        Assert::areEqual('Range::where', $r1->concat($r2)
                                            ->where(function($i) {
                                                        return 9 == $i;
                                                      })
                                            ->count(), 2);
    }
}

$tests = new LinqTests();

$r = new ReflectionClass('LinqTests');
foreach ($r->getMethods() as $m) {
    if (!$m->isPublic()) {
        continue;
    }
    
    if (0 != strpos($m->getName(), 'test_')) {
        continue;
    }
    
    $m->invoke($tests);
}

echo 'All tests are OK!';

?>
    </pre>
  </body>
</html>