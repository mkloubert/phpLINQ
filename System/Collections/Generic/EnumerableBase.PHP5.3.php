<?php

/**
 *  LINQ concept for PHP
 *  Copyright (C) 2015  Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 *    
 *    This library is free software; you can redistribute it and/or
 *    modify it under the terms of the GNU Lesser General Public
 *    License as published by the Free Software Foundation; either
 *    version 3.0 of the License, or (at your option) any later version.
 *    
 *    This library is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 *    Lesser General Public License for more details.
 *    
 *    You should have received a copy of the GNU Lesser General Public
 *    License along with this library.
 */


namespace System\Collections\Generic;

use \System\Collections\Collection;
use \System\Collections\Dictionary;
use \System\Linq\Grouping;
use \System\Linq\Lookup;


/**
 * A basic sequence (PHP 5.3).
 * 
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
abstract class EnumerableBase implements IEnumerable {
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::all()
     */
    public final function all($predicate) {
        $this->checkForFunctionOrThrow($predicate, 1, false);
        
        while ($this->valid()) {
            $i = $this->current();
            $this->next();
            
            if (!$predicate($i)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::any()
     */
    public final function any($predicate = null) {
        $this->checkForFunctionOrThrow($predicate);
        
        $predicate = self::toPredeciateSafe($predicate);
        
        while ($this->valid()) {
            $i = $this->current();
            $this->next();
            
            if ($predicate($i)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::average()
     */
    public final function average($defValue = null) {
        $result = $defValue;
        
        $i = 0;
        while ($this->valid()) {
            $item = $this->current();
        
            if (0 == $i++) {
                $result = $item;
            }
            else {
                $result += $item;
            }
        
            $this->next();
        }
        
        if ($i > 0) {
            $result = $result / $i;
        }
         
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::cast()
     */
    public final function cast($type) {
        $code = sprintf('return (%s)$item;', trim($type));
        
        return $this->select(function($item) use ($code) {
            return eval($code);
        });
    }
    
    /**
     * Checks if an object/value is a function and throws an exception
     * if not.
     * 
     * @param mixed $obj The object/value to check.
     * @param integer $argCount The required arguments.
     * @param boolean $ignoreNull Ignore (null) references or not.
     */
    protected function checkForFunctionOrThrow($obj,
                                               $argCount = 1,
                                               $ignoreNull = true) {
        if (is_null($obj)) {
            if ($ignoreNull) {
                return;
            }
        }    
        else {
            $funcParamCount = static::getFuncParamCount($obj);
            if (is_numeric($funcParamCount)) {
                return $argCount == $funcParamCount;
            }
        }
        
        $this->throwException(sprintf('Function with %s argument(s) required!',
                                      $argCount));
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::concat()
     */
    public final function concat($iterator) {
        if (is_array($iterator)) {
            $iterator = new \ArrayIterator($iterator);
        }
    
        return static::toEnumerable($this->concatInner($iterator));
    }
    
    private function concatInner($iterator) {
    	$result = array();
    	
        // first this elements
        while ($this->valid()) {
            $result[] = $this->current();
    
            $this->next();
        }
    
        // now other elements
        while ($iterator->valid()) {
            $result[] = $iterator->current();
    
            $iterator->next();
        }
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::contains()
     */
    public final function contains($item, $comparer = null) {
        $this->checkForFunctionOrThrow($comparer, 2);
        
        $comparer = static::getComparerSafe($comparer);
        
        while ($this->valid()) {
            $i = $this->current();
            $this->next();
            
            if ($comparer($i, $item)) {
                return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * (non-PHPdoc)
     * @see \Countable::count()
     */
    public function count() {
        $result = 0;
        while ($this->valid()) {
            ++$result;
            
            $this->next();
        }    
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Iterator::current()
     */
    // public abstract function current();
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::defaultIfEmpty()
     */
    public final function defaultIfEmpty($defValue = null) {
        return static::toEnumerable($this->defaultIfEmptyInner(func_get_args()));
    }
    
    private function defaultIfEmptyInner($defValues) {
    	$result = array();
    	
        if ($this->valid()) {
            do {
                $result[] = $this->current();
                $this->next();
            } while ($this->valid());
        } 
        else {
            foreach ($defValues as $i) {
                $result[] = $i;
            }
        }
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::distinct()
     */
    public final function distinct($comparer = null) {
        $this->checkForFunctionOrThrow($comparer, 2);
        
        $comparer = static::getComparerSafe($comparer);
        
        return static::toEnumerable($this->distinctInner($comparer));
    }
    
    private function distinctInner($comparer) {
    	$result = array();
    	
        $temp = array();
        while ($this->valid()) {
            $i = $this->current();
        
            // search for duplicate
            $alreadyInList = false;
            foreach ($temp as $ti) {
                if ($comparer($i, $ti)) {
                    // found duplicate
        
                    $alreadyInList = true;
                    break;
                }
            }
        
            if (!$alreadyInList) {
                $temp[] = $i;
                $result[] = $i;
            }
        
            $this->next();
        }
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::elementAtOrDefault()
     */
    public function elementAtOrDefault($index, $defValue = null) {
        $result = $defValue;
        
        while (($index >= 0) && $this->valid()) {
            $i = $this->current();
            $this->next();
        
            if (0 == $index--) {
                $result = $i;
                break;
            }
        }
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::except()
     */
    public final function except($second, $comparer = null) {
        $this->checkForFunctionOrThrow($comparer, 2);
        
        $comparer = static::getComparerSafe($comparer);
        
        return static::toEnumerable($this->exceptInner(static::toEnumerable($second),
                                                       $comparer));
    }
    
    private function exceptInner(IEnumerable $second, $comparer) {
    	$result = array();
    	
        $itemsToExclude = static::toEnumerable($second->distinct($comparer)
                                                       ->toArray());
         
        while ($this->valid()) {
            $i = $this->current();
            if (!$itemsToExclude->reset()
                                ->contains($i, $comparer)) {
                $result[] = $i;
            }
             
            $this->next();
        }
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::firstOrDefault()
     */
    public final function firstOrDefault($predicate = null, $defValue = null) {
        if (func_num_args() == 1) {
            if (!is_null($predicate) &&
                !is_callable($predicate)) {
                
                // handle first argument as default value
                $defValue = $predicate;
                $predicate = null;
            }
        }
        
        $this->checkForFunctionOrThrow($predicate);
        
        $predicate = self::toPredeciateSafe($predicate);
        
        $result = $defValue;
        while ($this->valid()) {
            $i = $this->current();

            $this->next();
            
            if ($predicate($i)) {
                $result = $i;
                break;
            }
        }
        
        return $result;
    }
    
    /**
     * Returns a non-null item comparer function.
     * 
     * @param callable $comparer The input value.
     * 
     * @return callable The comparer.
     */
    protected static function getComparerSafe($comparer) {
        if (is_null($comparer)) {
            $comparer = function($x, $y) {
                return $x == $y;
            };
        }
        
        return $comparer;
    }
    
    private static function getFuncParamCount($func) {
        if (is_null($func)) {
            return null;
        }
        
        if (is_callable($func)) {
            $r = new \ReflectionFunction($func);
            
            return count($r->getParameters());
        }
        
        return false;
    }

    private static function getKeySelectorOrWrap($keySelector) {
        if (is_null($keySelector)) {
            return null;
        }
        
        $result = $keySelector;
        
        if (1 === static::getFuncParamCount($result)) {
            $orgKeySelector = $result;
            
            $result = function($orgKey, $item) use ($orgKeySelector) {
                return $orgKeySelector($item);
            };
        }
        
        return $result;
    }
    
    private static function getKeySelectorSafe($keySelector) {
        if (is_null($keySelector)) {
            $keySelector = function($orgKey, $item) {
                return $orgKey;
            };
        }
    
        return $keySelector;
    }
    
    private static function getSortAlgoSafe($algo) {
        if (is_null($algo)) {
            $algo = function($x, $y) {
                if ($x > $y) {
                    return 1;
                }
                 
                if ($x < $y) {
                    return -1;
                }
                 
                return 0;
            };
        }
        
        return $algo;
    }
    
    private static function getStringSelectorSafe($selector) {
        if (is_null($selector)) {
            $selector = function($x) {
                return strval($x);
            };
        }
        
        return $selector;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::groupBy()
     */
    public final function groupBy($keySelector, $keyComparer = null) {
        $keySelector = static::getKeySelectorOrWrap($keySelector);
        
        $this->checkForFunctionOrThrow($keySelector, 2, false);
        $this->checkForFunctionOrThrow($keyComparer, 2);
         
        return static::toEnumerable($this->groupByInner($keySelector,
                                                        $keyComparer));
    }

    private function groupByInner($keySelector, $keyComparer) {
    	$result = array();
    	
        $dict = new Dictionary(null, $keyComparer);
         
        while ($this->valid()) {
            $i = $this->current();
            $k = $keySelector($this->key(), $i);
    
            if (!isset($dict[$k])) {
                $dict[$k] = new Dictionary();
            }
    
            $items = $dict[$k];
            $items[] = $i;
    
            $this->next();
        }
    
        foreach ($dict as $entry) {
            $result[] = new Grouping($entry->key(),
                                     $entry->value()
                                           ->reset()
                                           ->select(function($i) {
                                                        return $i->value();
                                                    }));
        }
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::groupJoin()
     */
    public final function groupJoin($inner,
                                    $outerKeySelector, $innerKeySelector,
                                    $resultSelector,
                                    $keyComparer = null) {
        
        $this->checkForFunctionOrThrow($resultSelector, 2, false);
        $this->checkForFunctionOrThrow($keyComparer, 2, true);
         
        $keyComparer = static::getComparerSafe($keyComparer);
         
        return static::toEnumerable($this->groupJoinIterator(static::toEnumerable($inner),
                                                             $outerKeySelector, $innerKeySelector,
                                                             $resultSelector,
                                                             $keyComparer));
    }
    
    private function groupJoinIterator(IEnumerable $inner,
                                       $outerKeySelector, $innerKeySelector,
                                       $resultSelector,
                                       $keyComparer) {

    	$result = array();
    	
        $keySelector = function($seqKey, $item) {
            return $item->key();
        };
         
        $grpOuter = $this->groupBy($outerKeySelector)
                         ->toDictionary($keySelector, $keyComparer);
        foreach ($grpOuter->keys() as $k) {
            $grpOuter[$k] = $grpOuter[$k]->getIterator();
        }
        
        $grpInner = $inner->groupBy($innerKeySelector)
                          ->toDictionary($keySelector, $keyComparer);
        foreach ($grpInner->keys() as $k) {
            $grpInner[$k] = $grpInner[$k]->getIterator()
                                         ->toArray();
        }
         
        foreach ($grpOuter as $entry) {
            $key = $entry->key();
        
            if (isset($grpInner[$key])) {
                foreach ($entry->value() as $o) {
                    $result[] = $resultSelector($o,
                                                static::toEnumerable($grpInner[$key]));
                }
            }
        }
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::intersect()
     */
    public final function intersect($second, $comparer = null) {
        $this->checkForFunctionOrThrow($comparer, 2);
         
        $comparer = static::getComparerSafe($comparer);
         
        return static::toEnumerable($this->intersectInner(static::toEnumerable($second),
                                                          $comparer));
    }
    
    private function intersectInner(IEnumerable $second, $comparer) {
    	$result = array();
    	
        $secondArray = $second->distinct($comparer)
                              ->toArray();
    
        while ($this->valid()) {
            $ci = $this->current();
            
            // search for matching item in second sequence
            foreach ($secondArray as $k => $v) {
                if (!$comparer($v, $ci)) {
                    // not found
                    continue; 
                }
                
                $result[] = $ci;
                unset($secondArray[$k]);
                
                break;
            }
                     
            $this->next();
        }
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::join()
     */
    public final function join($inner,
                               $outerKeySelector, $innerKeySelector,
                               $resultSelector,
                               $keyComparer = null) {
        
        $this->checkForFunctionOrThrow($resultSelector, 2, false);
        $this->checkForFunctionOrThrow($keyComparer, 2, true);
        
        $keyComparer = static::getComparerSafe($keyComparer);
        
        return static::toEnumerable($this->joinIterator(static::toEnumerable($inner),
                                                        $outerKeySelector, $innerKeySelector,
                                                        $resultSelector,
                                                        $keyComparer));
    }
    
    private function joinIterator(IEnumerable $inner,
                                  $outerKeySelector, $innerKeySelector,
                                  $resultSelector,
                                  $keyComparer) {
        
    	$result = array();
    	
        $keySelector = function($seqKey, $item) {
            return $item->key();
        };
        
        $grpOuter = $this->groupBy($outerKeySelector)
                         ->toDictionary($keySelector, $keyComparer);
        foreach ($grpOuter->keys() as $k) {
            $grpOuter[$k] = $grpOuter[$k]->getIterator()
                                         ->toArray();
        }

        $grpInner = $inner->groupBy($innerKeySelector)
                          ->toDictionary($keySelector, $keyComparer);
        foreach ($grpInner->keys() as $k) {
            $grpInner[$k] = $grpInner[$k]->getIterator()
                                         ->toArray();
        }
        
        foreach ($grpOuter as $entry) {
            $key = $entry->key();
            
            if (isset($grpInner[$key])) {
                foreach ($entry->value() as $o) {
                    foreach ($grpInner[$key] as $i) {
                        $result[] = $resultSelector($o, $i);
                    }
                }
            }
        }
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Iterator::key()
     */
    // public abstract function key();

    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::lastOrDefault()
     */
    public final function lastOrDefault($predicate = null, $defValue = null) {
        if (func_num_args() == 1) {
            if (!is_null($predicate) &&
                !is_callable($predicate)) {
                         
                // handle first argument as default value
                $defValue = $predicate;
                   $predicate = null;
              }
        }
        
        $this->checkForFunctionOrThrow($predicate);
        
        $predicate = self::toPredeciateSafe($predicate);
        
        $result = $defValue;
        while ($this->valid()) {
            $i = $this->current();
            if ($predicate($i)) {
                $result = $i;
            }

            $this->next();
        }
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::max()
     */
    public final function max($defValue = null) {
        $result = $defValue;
        
        $isFirst = true;
        while ($this->valid()) {
            $i = $this->current();
            
            if (!$isFirst) {
                if ($i > $result) {
                    $result = $i;
                }
            }
            else {
                $isFirst = false;
                $result = $i;
            }
            
            $this->next();
        }
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::min()
     */
    public final function min($defValue = null) {
        $result = $defValue;
        
        $isFirst = true;
        while ($this->valid()) {
            $i = $this->current();
            
            if (!$isFirst) {
                if ($i < $result) {
                    $result = $i;
                }
            }
            else {
                $isFirst = false;
                $result = $i;
            }
            
            $this->next();
        }
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::multiply()
     */
    public final function multiply($defValue = null) {
        $result = $defValue;
         
        $isFirst = true;
        while ($this->valid()) {
            $i = $this->current();
        
            if (!$isFirst) {
                $result *= $i;
            }
            else {
                $isFirst = false;
                $result = $i;
            }
        
            $this->next();
        }
         
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Iterator::next()
     */
    // public abstract function next();

    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::ofType()
     */
    public final function ofType($type) {
        $type = trim($type);
        
        return $this->where(function($item) use ($type) {
            if (is_object($item)) {
                $code = 'get_class($item) == $type';
            }
            else {
                $code = 'gettype($item) == $type';
            }
            
            return eval(sprintf('return %s;', $code));
        });
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::order()
     */
    public final function order($algo = null) {
        return $this->orderBy(function($x) { return $x; },
                              $algo);
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::orderBy()
     */
    public final function orderBy($sortSelector, $algo = null) {
        $this->checkForFunctionOrThrow($sortSelector, 1, false);
        $this->checkForFunctionOrThrow($algo, 2);
        
        $algo = static::getSortAlgoSafe($algo);
        
        return static::toEnumerable($this->orderByInner($sortSelector, $algo));
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::orderBy()
     */
    public final function orderByDescending($sortSelector, $algo = null) {
        $this->checkForFunctionOrThrow($sortSelector, 1, false);
        $this->checkForFunctionOrThrow($algo, 2);
         
        $algo = static::getSortAlgoSafe($algo);
        $descAlgo = function($x, $y) use ($algo) {
            return $algo($x, $y) * -1;
        };
        
        return static::toEnumerable($this->orderByInner($sortSelector, $descAlgo));
    }
    
    private function orderByInner($sortSelector, $algo) {
    	$result = array();
    	
        $items = array();
        while ($this->valid()) {
            $i = $this->current();
            
            $newItem   = array();
            $newItem[] = $sortSelector($i);
            $newItem[] = $i;
            
            $items[] = $newItem;
            
            $this->next();
        }
        
        usort($items, function($x, $y) use ($algo) {
                          return $algo($x[0], $y[0]);
                      });
        
        foreach ($items as $i) {
            $result[] = $i[1];
        }
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::orderDescending()
     */
    public final function orderDescending($algo = null) {
        return $this->orderByDescending(function($x) { return $x; },
                                        $algo);
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::product()
     */
    public final function product($defValue = null) {
        return $this->multiply($defValue);
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::reset()
     */
    public final function reset() {
        $this->rewind();
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::randomize()
     */
    public final function randomize($seed = null, $randomizer = null) {
        if (0 === static::getFuncParamCount($randomizer)) {
            $oldRandomizer = $randomizer;
            
            $randomizer = function($index, $item) use ($oldRandomizer) {
                 return $oldRandomizer();
            };
        }
        
        $this->checkForFunctionOrThrow($randomizer, 2);
        
        if (is_null($randomizer)) {
            // default
            
            $randomizer = function($index, $item) {
                return mt_rand();
            };
        }
        
        if (!is_null($seed)) {
            // seed
            
            $seeder = $seed;
            if (!is_callable($seeder)) {
                $seeder = function() use ($seed) {
                    mt_srand($seed);
                };
            }
            
            $seeder();
        }
        
        $index = 0;
        return $this->orderBy(function($item) use (&$index, $randomizer) {
                                  return $randomizer($index++, $item);
                              });
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::reverse()
     */
    public final function reverse() {
        $i = PHP_INT_MAX;
        return $this->orderBy(function($x) use (&$i) {
                                  return $i--;
                              });
    }
    
    /**
     * (non-PHPdoc)
     * @see \Iterator::rewind()
     */
    // public abstract function rewind();
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::select()
     */
    public final function select($selector) {
        $this->checkForFunctionOrThrow($selector, 1, false);
        
        return static::toEnumerable($this->selectInner($selector));
    }
    
    private function selectInner($selector) {
    	$result = array();
    	
        while ($this->valid()) {
            $result[] = $selector($this->current());
        
            $this->next();
        }
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::selectMany()
     */
    public final function selectMany($selector) {
        $this->checkForFunctionOrThrow($selector, 1, false);
        
        return static::toEnumerable($this->selectManyInner($selector));
    }
    
    private function selectManyInner($selector) {
    	$result = array();
    	
        while ($this->valid()) {
            $items = $selector($this->current());
            foreach ($items as $i) {
                $result[] = $i;
            }
        
            $this->next();
        }
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::sequenceEqual()
     */
    public final function sequenceEqual($second, $comparer = null) {
        $this->checkForFunctionOrThrow($comparer, 2);
        
        $comparer = static::getComparerSafe($comparer);
        
        $other = static::toEnumerable($second);
        
        while ($this->valid()) {
            $x = $this->current();
            $this->next();
            
            if (!$other->valid()) {
                return false;
            }
            
            $y = $other->current();
            $other->next();
            
            if (!$comparer($x, $y)) {
                return false;
            }
        }
        
        if ($other->valid()) {
            return false;
        }
        
        return true;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::skip()
     */
    public final function skip($count) {
        if ($count < 0) {
            $this->throwException('count value is invalid!');
        }
        
        return $this->skipWhile(function($item) use(&$count) {
            return $count-- > 0;
        });
    }

    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::singleOrDefault()
     */
    public final function singleOrDefault($predicate = null, $defValue = null) {
        if (func_num_args() == 1) {
            if (!is_null($predicate) &&
                !is_callable($predicate)) {
        
                // handle first argument as default value
                $defValue = $predicate;
                $predicate = null;
            }
        }
        
        $this->checkForFunctionOrThrow($predicate);
        
        $predicate = static::toPredeciateSafe($predicate);
        
        $result = $defValue;
        
        $matchCount = 0;
        while ($this->valid()) {
            $i = $this->current();
            if ($predicate($i)) {
                $result = $i;
                ++$matchCount;
            }
            
            $this->next();
        }
        
        if ($matchCount > 1) {
            throw new \Exception('Sequence contains more than one matching element!');
        }
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::skipWhile()
     */
    public final function skipWhile($predicate) {
         $this->checkForFunctionOrThrow($predicate, 1, false);
         
         return static::toEnumerable($this->skipWhileInner($predicate));
    }
    
    private function skipWhileInner($predicate) {
    	$result = array();
    	
        while ($this->valid()) {
            $i = $this->current();
            $this->next();
    
            if ($predicate($i)) {
                continue;
            }
    
            $result[] = $i;
        }
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::stringConcat()
     */
    public final function stringConcat($selector = null) {
        return $this->stringJoin('', $selector);
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::stringJoin()
     */
    public final function stringJoin($separator, $selector = null) {
        $this->checkForFunctionOrThrow($selector);
        
        $selector = static::getStringSelectorSafe($selector);
        
        $result = '';
        
        $isFirst = true;
        while ($this->valid()) {
            if (!$isFirst) {
                $result .= strval($separator);
            }
            else {
                $isFirst = false;
            }
            
            $result .= $selector($this->current());
            
            $this->next();
        }
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::sum()
     */
    public final function sum($defValue = null) {
        $result = $defValue;
        
        $isFirst = true;
        while ($this->valid()) {
            $i = $this->current();
            
            if ($isFirst) {
                $result = $i;
                $isFirst = false;    
            }
            else {
                $result += $i;
            }
            
            $this->next();
        }
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::take()
     */
    public final function take($count) {
        if ($count < 0) {
            $this->throwException('count value is invalid!');
        }
        
        return $this->takeWhile(function($item) use(&$count) {
            return $count-- > 0;
        });
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::takeWhile()
     */
    public final function takeWhile($predicate) {
        $this->checkForFunctionOrThrow($predicate, 1, false);
        
        return static::toEnumerable($this->takeWhileInner($predicate));
    }
    
    private function takeWhileInner($predicate) {
    	$result = array();
    	
        while ($this->valid()) {
            $i = $this->current();
            $this->next();
    
            if (!$predicate($i)) {
                break;
            }
    
            $result[] = $i;
        }
        
        return $result;
    }
    
    /**
     * Throws an exception for that sequence.
     * 
     * @param string $message The message.
     * @param number $code The code.
     * @param string $previous The inner/previous exception.
     * 
     * @throws EnumerableException The thrown exception.
     */
    protected function throwException($message = null,
                                      $code = 0,
                                      $previous = null) {
        
        throw new EnumerableException($this,
                                      $message, $code, $previous);
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::toArray()
     */
    public final function toArray($keySelector = null) {
        $keySelector = static::getKeySelectorOrWrap($keySelector);
        
        $this->checkForFunctionOrThrow($keySelector, 2);
        if (is_null($keySelector)) {
            // default
            
            $keySelector = function($index, $item) {
                return null;
            };
        }
        
        $i = 0;
        $result = array();
        while ($this->valid()) {
            $ci = $this->current();
            
            $key = $keySelector($i++, $ci);
            if (is_null($key)) {
                $result[] = $ci;
            }
            else {
                $result[$key] = $ci;
            }

            $this->next();
        }
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::toDictionary()
     */
    public function toDictionary($keySelector = null, $keyComparer = null) {
        $keySelector = static::getKeySelectorOrWrap($keySelector);
        
        $this->checkForFunctionOrThrow($keySelector, 2);
        $this->checkForFunctionOrThrow($keyComparer, 2);
    
        $keySelector = static::getKeySelectorSafe($keySelector);
        
        $result = new Dictionary(null, $keyComparer);
        while ($this->valid()) {
            $i = $this->current();
            $k = $keySelector($this->key(), $i);
    
            $result->add($k, $i);
    
            $this->next();
        }
    
        return $result;
    }

    /**
     * Wraps an object to a sequence.
     *
     * @param mixed $input The input value/object.
     *
     * @return \System\Collections\Generic\IEnumerable The wrapped object.
     */
    protected static function toEnumerable($input) {
        return false;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::toList()
     */
    public final function toList() {
        return new Collection($this);
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::toLookup()
     */
    public final function toLookup($keySelector = null, $keyComparer = null,
                                   $elementSelector = null) {
        
        $this->checkForFunctionOrThrow($elementSelector, 1);

        $elements = $this;
        if (!is_null($elementSelector)) {
            $elements = $this->select($elementSelector);
        }
        
        $grps = $elements->groupBy($keySelector,
                                   $keyComparer);
        
        return new Lookup($grps);
    }
    
    private static function toPredeciateSafe($predicate, $defValue = true) {
        if (is_null($predicate)) {
            $predicate = function($i) use ($defValue) {
                return $defValue;
            };
        }
        
        return $predicate;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::toSet()
     */
    public final function toSet($comparer = null) {
        $result = new Set($comparer);
        while ($this->valid()) {
            $result->add($this->current());
            
            $this->next();
        }
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::union()
     */
    public final function union($second, $comparer = null) {
        return $this->concat($second)
                    ->distinct($comparer);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Iterator::valid()
     */
    // public abstract function valid();
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::where()
     */
    public final function where($predicate) {
        $this->checkForFunctionOrThrow($predicate, 1, false);
        
        return static::toEnumerable($this->whereInner($predicate));
    }
    
    private function whereInner($predicate) {
    	$result = array();
    	
        while ($this->valid()) {
            $i = $this->current();
            
            if ($predicate($i)) {
                $result[] = $i;
            }
            
            $this->next();
        }
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IEnumerable::zip()
     */
    public final function zip($second, $selector) {
        $this->checkForFunctionOrThrow($selector, 2, false);
        
        return static::toEnumerable($this->zipInner(static::toEnumerable($second),
                                                    $selector));
    }
    
    private function zipInner(IEnumerable $second, $selector) {
    	$result = array();
    	
        while ($this->valid() && $second->valid()) {
            $result[] = $selector($this->current(),
                                  $second->current());
            
            $this->next();
            $second->next();
        }
        
        return $result;
    }
}
