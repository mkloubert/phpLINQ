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

$seq = Enumerable::fromValues(5979, 23979, null, 1781, 241279);

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
- [ ] defaultIfEmpty() method
- [ ] distinct() method
- [ ] elementAtOrDefault() method
- [ ] except() method
- [ ] groupBy() method
- [ ] intersect() method
- [ ] moveNext() method
- [x] ofType() method
- [ ] orderBy() method
- [ ] orderByDescending() method
- [x] skipWhile() method
- [ ] singleOrDefault() method
- [x] takeWhile() method
