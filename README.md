# phpLINQ

A LINQ concept for PHP.

## Requirements

* PHP 5.5+ (because it uses [Generator syntax](http://php.net/manual/en/language.generators.syntax.php))

## Getting started

Include class files manually or via [autloader](http://php.net/manual/en/language.oop5.autoload.php):

```php
require_once './System/Collections/Generic/IEnumerable.php';
require_once './System/Collections/Generic/EnumerableBase.php';
require_once './System/Collections/Generic/EnumerableException.php';
require_once './System/Linq/Enumerable.php';
```

Create a sequence with methods that are similar to [LINQ extension methods](https://msdn.microsoft.com/en-us/library/system.linq.enumerable%28v=vs.100%29.aspx):

```php
use \System\Linq;

$seq = Enumerable::fromArray(array(5979, 'TM', null, "MK", 23979));
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
                       .where(function($item) {
                                  return !empty($item);
                              })    // filter out all values that are empty
                       .skip(1)    // skip the first element ('5979')
                       .take(2);    // take the next 2 elements from current position
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

$seq = mew Enumerable(createIterator());

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

