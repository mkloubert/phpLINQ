# phpLINQ

A [LINQ](https://en.wikipedia.org/wiki/Language_Integrated_Query) concept for [PHP](https://en.wikipedia.org/wiki/PHP).

Most methods are chainable as in [.NET](https://en.wikipedia.org/wiki/.NET_Framework) context.

## Documentation

Have a look at the [wiki](./wiki).

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

## Classes and interfaces

### IDictionary

This is an implementation of the [.NET dictionary](https://msdn.microsoft.com/en-us/library/system.collections.idictionary%28v=vs.110%29.aspx).

#### Example

```php
use \System\Collections;


$dict = new Dictionary();


// add/set entries
$dict['TM'] = 5979;
$dict->add('MK', 23979);
$dict->add('YS', '1981-07-01');

// number of entries: 3
$c = count($dict);

// enumerate entries
foreach ($dict as $entry) {
    $k = $entry->key();
    $v = $entry->value();
    
    //TODO
}

// LINQ
// 
// map entries to general objects (stdClass)
foreach ($dict->select(function($e) {
                           $o    = new \stdClass();
                           $o->k = trim(strtolower($e->key()));
                           $o->v = trim($e->value());
                           
                           return $o;
                       }) as $entry) {
                       
    $k = $entry->k;    // key
    $v = $entry->v;    // value
    
    //TODO
}

// enumerate keys
foreach ($dict->keys() as $key) {
    // [0] 'TM'
    // [1] 'MK'
    // [2] 'YS'
}

// enumerate values
foreach ($dict->values() as $val) {
    // [0] 5979
    // [1] 23979
    // [2] '1981-07-01'
}

// remove entries
$dict->remove('YS');
unset($dict['MK']);

// clear
$dict->clear();
```

