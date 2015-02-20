# phpLINQ

A [LINQ](https://en.wikipedia.org/wiki/Language_Integrated_Query) concept for [PHP](https://en.wikipedia.org/wiki/PHP).

Most methods are chainable as in [.NET](https://en.wikipedia.org/wiki/.NET_Framework) context.

Here you can find the [DOCUMENTATION](https://github.com/mkloubert/phpLINQ/wiki).

## Requirements

* PHP 5.5+ (because it uses [Generator syntax](http://php.net/manual/en/language.generators.syntax.php))

## Examples

### Example 1

Create a sequence from an [array](http://php.net/manual/en/language.types.array.php).

```php
use \System\Linq;

$seq = Enumerable::fromValues(5979, 23979, null, 23979, 1781, 241279);

$newSeq = $seq->select(function($item) {
                           return strval($item);
                       })  // transform all values
                           // to string
              ->where(function($item) {
                          return !empty($item);
                      })    // filter out all values that are empty
              ->skip(1)    // skip the first element ('5979')
              ->take(3);    // take the next 2 elements from current position
                            // ('23979', '23979' and '1781')
              ->distinct()    // remove duplicates
              ->order();    // sort
                                    
foreach ($newSeq as $item) {
    // [0] '1781'
    // [1] '23979'
}
```

### Example 2

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

## TODO

- [x] cast() method
- [x] defaultIfEmpty() method
- [x] distinct() method
- [x] elementAtOrDefault() method
- [ ] except() method
- [x] groupBy() method
- [ ] intersect() method
- [ ] moveNext() method
- [x] ofType() method
- [x] orderBy() method
- [x] orderByDescending() method
- [x] skipWhile() method
- [x] singleOrDefault() method
- [x] takeWhile() method
