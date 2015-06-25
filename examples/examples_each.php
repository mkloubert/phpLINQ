<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


require_once './bootstrap.inc.php';


$pageTitle = 'each()';

// example #1
$examples[] = new Example();
$examples[0]->sourceCode = 'use \\System\\Linq\\Enumerable;
    
$seq = Enumerable::range(1, 10);

$result = $seq->each(function($x, $ctx) {
                         //  $ctx->cancel   => cancel operation or not (is FALSE by default)
                         //  $ctx->index    => zero based index
                         //  $ctx->isFirst  => indicates if this is the first element or not
                         //  $ctx->isLast   => indicates if this is the last element or not
                         //  $ctx->iterator => the underlying iterator
                         //  $ctx->key      => the current key
                         //  $ctx->nextVal  => the value of prevVal for the next element
                         //                    (is set to NULL at the beginning of each call)
                         //  $ctx->prevVal  => the value of nextVal from the previous call
                         //  $ctx->tag      => a value that can be defined for all invocations
                         //                    of that callable / function
                         //                    it is not resetted
                         //  $ctx->value    => same as $x


                         // define value for $result (the result of each() method)
                         $ctx->result += $x;
                     });

// 55
echo "{$result}\n";

';


require_once './shutdown.inc.php';
