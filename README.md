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

## Example

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

## Methods

### all

Determines whether all elements of the sequence satisfy a condition (s. [All<TSource>(TSource)](https://msdn.microsoft.com/en-us/library/bb548541%28v=vs.100%29.aspx)).

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

Determines whether any element of the sequence exists or satisfies a condition. (s. [Any<TSource>](https://msdn.microsoft.com/en-us/library/system.linq.enumerable.any%28v=vs.100%29.aspx)).

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

Concatenates that sequence with another. (s. [Concat<TSource>(TSource)](https://msdn.microsoft.com/en-us/library/bb302894%28v=vs.100%29.aspx)).

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

