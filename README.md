# phpLINQ

A [LINQ](https://en.wikipedia.org/wiki/Language_Integrated_Query) concept for [PHP](https://en.wikipedia.org/wiki/PHP).

Most methods are chainable as in [.NET](https://en.wikipedia.org/wiki/.NET_Framework) context.

Here you can find the [DOCUMENTATION](https://github.com/mkloubert/phpLINQ/wiki) in the wiki or the [API](http://phplinq.marcel-kloubert.eu/api/) documentation.

## Requirements

* PHP 5.3+ ([branch v2](https://github.com/mkloubert/phpLINQ/tree/v2) or [branch v1](https://github.com/mkloubert/phpLINQ/tree/v1))
* PHP 7+ (master branch)

## Features

* [Lambda expressions](https://github.com/mkloubert/phpLINQ/wiki/Lambda-expression)
* Any kind of [lists or iterators](https://github.com/mkloubert/phpLINQ/wiki/Sequence) can be used
* Most methods are chainable as in [.NET](https://en.wikipedia.org/wiki/.NET_Framework) context

## Examples

A complete list can be found at the [live example page](https://github.com/mkloubert/phpLINQ/tree/master/examples).

### Example 1

Create a sequence from a list of values.

```php
use \System\Linq;

$seq = Enumerable::fromValues(5979, 23979, null, 23979, 1781, 241279);

$newSeq = $seq->select('$x => (string)$x')  // transform all values
                                            // to string
              ->where('$x => !empty($x)')    // filter out all values that are empty
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

Create a sequence from any [Iterator](https://github.com/mkloubert/phpLINQ/wiki/Sequence).

```php
use \System\Linq;

function createIterator() {
    yield 5979;
    yield 23979;
    yield 1781;
    yield 241279;
}

$seq = Enumerable::create(createIterator());

// ...
```

## Implemented

* [aggregate()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.aggregate()-method)
* [all()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.all()-method)
* [any()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.any()-method)
* [average()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.average()-method)
* [cast()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.cast()-method)
* [concat()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.concat()-method)
* [contains()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.contains()-method)
* [count()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.count()-method)
* [defaultIfEmpty()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.defaultIfEmpty()-method)
* [distinct()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.distinct()-method)
* [elementAt()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.elementAt()-method)
* [elementAtOrDefault()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.elementAtOrDefault()-method)
* [except()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.except()-method)
* [first()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.first()-method)
* [firstOrDefault()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.firstOrDefault()-method)
* [groupBy()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.groupBy()-method)
* [groupJoin()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.groupJoin()-method)
* [intersect()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.intersect()-method)
* [join()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.join()-method)
* [last()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.last()-method)
* [lastOrDefault()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.lastOrDefault()-method)
* [max()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.max()-method)
* [min()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.min()-method)
* [ofType()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.ofType()-method)
* [orderBy()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.orderBy()-method)
* [orderByDescending()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.orderByDescending()-method)
* [reverse()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.reverse()-method)
* [select()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.select()-method)
* [selectMany()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.selectMany()-method)
* [sequenceEqual()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.sequenceEqual()-method)
* [single()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.single()-method)
* [singleOrDefault()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.singleOrDefault()-method)
* [skip()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.skip()-method)
* [skipWhile()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.skipWhile()-method)
* [sum()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.sum()-method)
* [take()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.take()-method)
* [takeWhile()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.takeWhile()-method)
* [toArray()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.toArray()-method)
* [toDictionary()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.toDictionary()-method)
* [toList()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.toList()-method)
* [toLookup()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.toLookup()-method)
* [union()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.union()-method)
* [where()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.where()-method)
* [zip()](https://github.com/mkloubert/phpLINQ/wiki/IEnumerable.zip()-method)
