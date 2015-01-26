# phpLINQ

A [LINQ](https://en.wikipedia.org/wiki/Language_Integrated_Query) concept for [PHP](https://en.wikipedia.org/wiki/PHP).

Most methods are chainable as in [.NET](https://en.wikipedia.org/wiki/.NET_Framework) context.

## Requirements

* PHP 5.5+ (because it uses [Generator syntax](http://php.net/manual/en/language.generators.syntax.php))

## Getting started

Include class files manually or via [autoloader](http://php.net/manual/en/language.oop5.autoload.php):

```php
require_once './System/Collections/Generic/IEnumerable.php';
require_once './System/Collections/Generic/EnumerableBase.php';
require_once './System/Collections/Generic/EnumerableException.php';
require_once './System/Collections/DictionaryEntry.php';
require_once './System/Collections/IDictionary.php';
require_once './System/Collections/Dictionary.php';
require_once './System/Linq/Enumerable.php';
```

Create a sequence with methods that are similar to [LINQ extension methods](https://msdn.microsoft.com/en-us/library/system.linq.enumerable%28v=vs.100%29.aspx):

```php
use \System\Linq;

$seq = Enumerable::fromValues(5979, 'TM', null, "MK", 23979);
foreach ($seq as $item) {
    //TODO
}
```

## Example 1

Create a sequence from an [array](http://php.net/manual/en/language.types.array.php).

```php
use \System\Linq;

$seq = Enumerable::fromArray(array(5979, 23979, null, 1781, 241279));

$transformedSeq = $seq->select(function($item) {
                                   return strval($item);
                               })  // cast all values to string
                      ->where(function($item) {
                                  return !empty($item);
                              })    // filter out all values that are empty
                      ->skip(1)    // skip the first element ('5979')
                      ->take(2);    // take the next 2 elements from current position
                                    // ('23979' and '1781')
                                    
foreach ($transformedSeq as $item) {
    // [0] '23979'
    // [1] '1781'
}
```

## Example 2

Create a sequence from any [Iterator](http://php.net/manual/en/class.iterator.php).

```php
use \System\Linq;

function createIterator() {
    yield 5979;
    yield 23979;
    yield 1781;
    yield 241279;
}

$seq = new Enumerable(createIterator());

// ...
```

## Methods

### all

Determines whether all elements of the sequence satisfy a condition (s. [All(TSource)](https://msdn.microsoft.com/en-us/library/bb548541%28v=vs.100%29.aspx)).

```php
use \System\Linq;

$seq = Enumerable::fromArray(array(5979, 'TM', false, "MK", 23979));

// (false), because 3rd element is an empty value
$seq->reset();
$a1 = $seq->all(function($item) {
    return !empty($item);
});

// (true), because ALL values are not (null)
$seq->reset();
$a2 = $seq->all(function($item) {
    return !is_null($item);
});
```

### any

Determines whether any element of the sequence exists or satisfies a condition. (s. [Any](https://msdn.microsoft.com/en-us/library/system.linq.enumerable.any%28v=vs.100%29.aspx)).

```php
use \System\Linq;

$seq1 = Enumerable::fromArray(array(5979, 'TM', null, "MK", 23979));
$seq2 = Enumerable::createEmpty();

// (true), because 3rd element is (null)
$a1 = $seq1->any(function($item) {
    return is_null($item);
});

// (false), because sequence contains no elements
$a2 = $seq2->any();
```

### average

Computes the average of that sequence. (s. [Average()](https://msdn.microsoft.com/en-us/library/system.linq.enumerable.average%28v=vs.90%29.aspx)).

```php
use \System\Linq;

$seq1 = Enumerable::fromValues(1, 2, 3, 4, 5, 6);
$seq2 = Enumerable::createEmpty();

// 12
$a1 = $seq1->average();

// 'TM', because sequence is empty
$a2 = $seq2->average('TM');
```

### cast

Casts all elements of the sequence to a new type. (s. [Cast()](https://msdn.microsoft.com/en-us/library/vstudio/bb341406%28v=vs.100%29.aspx)).

```php
use \System\Linq;

$seq = Enumerable::fromArray(array(1, 2, 3));

foreach ($seq->cast('string') as $item) {
    // [0] '1'
    // [1] '2'
    // [2] '3'
}
```

### concat

Concatenates that sequence with another. (s. [Concat(TSource)](https://msdn.microsoft.com/en-us/library/bb302894%28v=vs.100%29.aspx)).

```php
use \System\Linq;

$seq1 = Enumerable::fromArray(array(5979, 'TM', null));
$seq2 = Enumerable::fromArray("MK", 23979);

foreach ($seq1->concat($seq2) as $item) {
    // [0] 5979
    // [1] 'TM'
    // [2] (null)
    // [3] "MK"
    // [4] 23979
}
```

### contains

Determines whether the sequence contains a specified element. (s. [Contains(TSource)](https://msdn.microsoft.com/en-us/library/system.linq.enumerable.contains%28v=vs.100%29.aspx)).

```php
use \System\Linq;

$seq = Enumerable::fromArray(array(5979, 'TM', null));

// (true)
$seq->reset();
$a1 = $seq->contains("TM");

// (false)
$seq->reset();
$a2 = $seq->contains(23979);
```

### count

Counts the elements of that sequence. (s. [Count()](https://msdn.microsoft.com/en-us/library/system.linq.enumerable.count%28v=vs.100%29.aspx))

```php
use \System\Linq;

$seq = Enumerable::fromArray(array(5979, 'TM', null));

// 3
$a1 = $seq->count();

// other way
$seq->reset();
$a2 = count($seq);
```

### createEmpty

Creates a new empty sequence. (s. [Empty()](https://msdn.microsoft.com/en-us/library/bb341042%28v=vs.100%29.aspx)).

```php
use \System\Linq;

$seq = Enumerable::createEmpty();
```

### firstOrDefault

Returns the first element of the sequence, or a default value if no element is found. (s. [FirstOrDefault()](https://msdn.microsoft.com/en-us/library/system.linq.enumerable.firstordefault%28v=vs.100%29.aspx)).

```php
use \System\Linq;

$seq = Enumerable::fromArray(array(5979, 'TM', null));

// 5979
$seq->reset();
$a1 = $seq->firstOrDefault();

// 666, because 'MK' does not exist
$seq->reset();
$a2 = $seq->firstOrDefault(function($item) {
                               return $item == 'MK';
                           }, 666);
```

### fromArray

Creates a new sequence from a PHP array.

```php
use \System\Linq;

$seq = Enumerable::fromArray(array(5979, 'TM', null));
```

### fromValues

Creates a new sequence from a list of values.

```php
use \System\Linq;

$seq = Enumerable::fromValues('TM', 5979, 'MK', 23979);
```

### lastOrDefault

Returns the last element of the sequence, or a default value if no element is found. (s. [LastOrDefault()](https://msdn.microsoft.com/en-us/library/system.linq.enumerable.lastordefault%28v=vs.100%29.aspx)).

```php
use \System\Linq;

$seq = Enumerable::fromArray(array(5979, 'TM', null));

// (null)
$seq->reset();
$a1 = $seq->lastOrDefault();

// 666, because 23979 does not exist
$seq->reset();
$a2 = $seq->lastOrDefault(function($item) {
                              return $item == 23979;
                          }, 666);
```

### max

Gets the maximum value of that sequence. (s. [Max()](https://msdn.microsoft.com/en-us/library/system.linq.enumerable.max%28v=vs.100%29.aspx)).

```php
use \System\Linq;

$seq1 = Enumerable::fromArray(array(239, 5979, 1));
$seq2 = Enumerable::createEmpty();

// 5979
$a1 = $seq1->max();

// 666, because no element found
$a2 = $seq2->max(666);
```

### min

Gets the minimum value of that sequence. (s. [Min()](https://msdn.microsoft.com/en-us/library/system.linq.enumerable.min%28v=vs.100%29.aspx)).

```php
use \System\Linq;

$seq1 = Enumerable::fromArray(array(239, 5979, 1));
$seq2 = Enumerable::createEmpty();

// 1
$a1 = $seq1->min();

// 666, because no element found
$a2 = $seq2->min(666);
```

### multiply

Multiplies all values of that sequence.

```php
use \System\Linq;

$seq1 = Enumerable::fromValues(1, 2, 3, 4, 5, 6);
$seq2 = Enumerable::createEmpty();

// 6! = 720
$a1 = $seq1->multiply();

// '1979-09-05', because sequence is empty
$a2 = $seq2->multiply('1979-09-05');
```

### ofType

Filters all elements of the sequence with a specific type. (s. [OfType()](https://msdn.microsoft.com/en-us/library/vstudio/bb360913%28v=vs.100%29.aspx)).

```php
use \System\Linq;

$seq = Enumerable::fromArray(array(1, null, '2', 3));

foreach ($seq->ofType('string') as $item) {
    // [0] '2'
}
```

### range

Generates a new sequence of numbers within a specified range. (s. [Range()](https://msdn.microsoft.com/en-us/library/system.linq.enumerable.range%28v=vs.100%29.aspx)).

```php
use \System\Linq;

// 2 - 11
$seq1 = Enumerable::range(2, 10);

// 2, 4, 6, 8 ... 20
$seq2a = Enumerable::range(2, 10, 2);

// other way to do this
$seq2b = Enumerable::range(2, 10, function($value, $index) {
                                      return 1 + 1;
                                  });
```

### repeat

Generates a new sequence that contains one repeated value or object. (s. [Repeat()](https://msdn.microsoft.com/en-us/library/bb348899%28v=vs.100%29.aspx)).

```php
use \System\Linq;

// 5979 elements of 'TM' string
$seq = Enumerable::repeat(5979, 'TM');
```

### select

Projects each element of that sequence to a new value. (s. [Select()](https://msdn.microsoft.com/en-us/library/system.linq.enumerable.select%28v=vs.100%29.aspx)).

```php
use \System\Linq;

$seq1 = Enumerable::fromArray(array(239, 5979, 1));

// convert items to strings
$seq2 = $seq1->select(function($item) {
                          return strval($item);
                      });
```

### selectMany

Projects each element (that have to be iterators or arrays) of that sequence to a new sequence and flattens the resulting sequences into one sequence. (s. [SelectMany()](https://msdn.microsoft.com/en-us/library/system.linq.enumerable.selectmany%28v=vs.100%29.aspx)).

```php
use \System\Linq;

$seq1 = Enumerable::fromArray(array(1, 2, 3));

// convert items to strings
$seq2 = $seq1->selectMany(function($item) {
                              return array($item, $item * 10, $item * 100);
                          });

foreach ($seq2 as $item) {
    // [0] 1
    // [1] 10
    // [2] 100
    // [3] 2
    // [4] 20
    // [5] 200
    // [6] 3
    // [7] 30
    // [8] 300
}
```

Other way:

```php
use \System\Linq;

function selectorFunc($item) {
    yield $item;
    yield $item * 10;
    yield $item * 100;
}

$seq1 = Enumerable::fromArray(array(1, 2, 3));

// convert items to strings
$seq2 = $seq1->selectMany('selectorFunc');

// ...
```

### reset

Same as [rewind()](http://php.net/manual/en/iterator.rewind.php).

### skip

Skips a number of elements. (s. [Skip()](https://msdn.microsoft.com/en-us/library/bb358985%28v=vs.100%29.aspx)).

```php
use \System\Linq;

$seq = Enumerable::fromArray(array(239, 5979, 22));

foreach ($seq->skip(1) as $item) {
    // [0] 5979
    // [1] 22
}
```

### skipWhile

Bypasses elements in that sequence as long as a specified condition is true and then returns the remaining elements. (s. [SkipWhile()](https://msdn.microsoft.com/en-us/library/vstudio/system.linq.enumerable.skipwhile%28v=vs.100%29.aspx)).

```php
use \System\Linq;

$seq = Enumerable::fromArray(array(239, 5979, 22));

$predicate = function($item) {
    return $item == 239; 
};

foreach ($seq->skipWhile($predicate) as $item) {
    // [0] 5979
    // [1] 22
}
```

### sum

Calculates the sum of all elements of that sequence. (s. [Sum()](https://msdn.microsoft.com/en-us/library/system.linq.enumerable.sum%28v=vs.100%29.aspx)).

```php
use \System\Linq;

$seq = Enumerable::fromArray(array(1, 20, 300));

// 321
$s = $seq->sum();
```

### take

Takes a number of elements. (s. [Take()](https://msdn.microsoft.com/en-us/library/bb503062%28v=vs.100%29.aspx)).

```php
use \System\Linq;

$seq = Enumerable::fromArray(array(239, 5979, 1));

foreach ($seq->take(2) as $item) {
    // [0] 239
    // [1] 5979
}
```

### takeWhile

Takes elements in that sequence as long as a specified condition is true. (s. [TakeWhile()](https://msdn.microsoft.com/en-us/library/vstudio/system.linq.enumerable.takewhile%28v=vs.100%29.aspx)).

```php
use \System\Linq;

$seq = Enumerable::fromArray(array(239, 5979, 22));

$predicate = function($item) {
    return $item != 22; 
};

foreach ($seq->takeWhile($predicate) as $item) {
    // [0] 239
    // [1] 5979
}
```

### toArray

Converts that sequence to a new PHP array. (s. [ToArray()](https://msdn.microsoft.com/en-us/library/bb298736%28v=vs.100%29.aspx)).

```php
use \System\Linq;

$seq = Enumerable::fromArray(array(239, 5979, 1));

// [0] => 239
// [1] => 5979
// [2] => 1
$arr = $seq->toArray();

$a1 = is_array($arr);    // (true)
```

### toDictionary

Converts that sequence to a new PHP array that is similar to a [hashtable / dictionary](https://msdn.microsoft.com/en-us/library/system.collections.idictionary%28v=vs.110%29.aspx). (s. [ToDictionary()](https://msdn.microsoft.com/en-us/library/system.linq.enumerable.todictionary%28v=vs.100%29.aspx)).

The original key values for the result array are taken from [key()](php.net/manual/en/iterator.key.php) method.

```php
use \System\Linq;

$seq = Enumerable::fromArray(array('a' => 239,
                                   'b' => 5979,
                                   'c' => 1));

$seq->reset();

// ['a'] => 239
// ['b'] => 5979
// ['c'] => 1
$dict1 = $seq->toDictionary();


$seq->reset();

// ['a :: 239']  => 239
// ['b :: 5979'] => 5979
// ['c :: 1']    => 1
$dict2 = $seq->toDictionary(function($key, $item) {
                                return sprintf('%s :: %s',
                                               $key,
                                               $item);
                            });
```

For the new dictionary you can define a custom key comparer function:

```php
use \System\Linq;

$seq = Enumerable::fromArray(array('a' => 239,
                                   'B' => 5979));

$dict = $seq->toDictionary(null,

                           // compares keys case-insensitive
                           // without whitespace at the beginning
                           // and the end
                           function($x, $y) {
                               return trim(strtolower($x)) ==
                                      trim(strtolower($y));
                           });
                           
// all are (true)
// because all will be mapped to 'a'
$a1 = isset($dict['a']);
$a2 = isset($dict['A']);
$a3 = isset($dict[' a  ']);
$a4 = isset($dict['  A  ']);

// all will return 5979
// because all will be mapped to 'B'
$b1 = $dict['b'];
$b2 = $dict['B'];
$b3 = $dict[' b'];
$b4 = $dict[' B  '];
```

### where

Filters the elements of that sequence. (s. [Where()](https://msdn.microsoft.com/en-us/library/system.linq.enumerable.where%28v=vs.100%29.aspx)).

```php
use \System\Linq;

$seq = Enumerable::fromArray(array(1, 2, 3, 4, 5, 6, 8));

foreach ($seq->where(function($i) {
                         return ($i % 2) == 0;
                     }) as $item) {
    
    // [0] 2
    // [1] 4
    // [2] 6
    // [3] 8
}
```
