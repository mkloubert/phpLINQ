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
              ->take(3)    // take the next 3 elements from current position
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

## Implemented

* [all()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.all()-method)
* [any()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.any()-method)
* [average()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.average()-method)
* [cast()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.cast()-method)
* [concat()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.concat()-method)
* [contains()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.contains()-method)
* [count()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.count()-method)
* [defaultIfEmpty()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.defaultIfEmpty()-method)
* [distinct()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.distinct()-method)
* [elementAtOrDefault()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.elementAtOrDefault()-method)
* [except()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.except()-method)
* [firstOrDefault()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.firstOrDefault()-method)
* [groupBy()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.groupBy()-method)
* [intersect()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.intersect()-method)
* [lastOrDefault()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.lastOrDefault()-method)
* [max()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.max()-method)
* [min()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.min()-method)
* [ofType()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.ofType()-method)
* [orderBy()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.orderBy()-method)
* [orderByDescending()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.orderByDescending()-method)
* [select()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.select()-method)
* [selectMany()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.selectMany()-method)
* [singleOrDefault()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.singleOrDefault()-method)
* [skip()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.skip()-method)
* [skipWhile()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.skipWhile()-method)
* [sum()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.sum()-method)
* [take()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.take()-method)
* [takeWhile()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.takeWhile()-method)
* [toArray()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.toArray()-method)
* [toDictionary()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.toDictionary()-method)
* [union()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.union()-method)
* [where()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.where()-method)
* [zip()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.zip()-method)


## TODO

- [ ] moveNext() method

