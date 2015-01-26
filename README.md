# phpLINQ

A [LINQ](https://en.wikipedia.org/wiki/Language_Integrated_Query) concept for [PHP](https://en.wikipedia.org/wiki/PHP).

Most methods are chainable as in [.NET](https://en.wikipedia.org/wiki/.NET_Framework) context.

## Documentation

Have a look at the [wiki](https://github.com/mkloubert/phpLINQ/wiki).

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
