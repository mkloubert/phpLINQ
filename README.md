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

## Methods

### all

Determines whether all elements of a sequence satisfy a condition (s. [All<TSource>(TSource)](https://msdn.microsoft.com/en-us/library/bb548541%28v=vs.100%29.aspx)).

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

