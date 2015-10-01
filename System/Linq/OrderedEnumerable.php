<?php

/**********************************************************************************************************************
 * phpLINQ (https://github.com/mkloubert/phpLINQ)                                                                     *
 *                                                                                                                    *
 * Copyright (c) 2015, Marcel Joachim Kloubert <marcel.kloubert@gmx.net>                                              *
 * All rights reserved.                                                                                               *
 *                                                                                                                    *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the   *
 * following conditions are met:                                                                                      *
 *                                                                                                                    *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the          *
 *    following disclaimer.                                                                                           *
 *                                                                                                                    *
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the       *
 *    following disclaimer in the documentation and/or other materials provided with the distribution.                *
 *                                                                                                                    *
 * 3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote    *
 *    products derived from this software without specific prior written permission.                                  *
 *                                                                                                                    *
 *                                                                                                                    *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, *
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE  *
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, *
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR    *
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,  *
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE   *
 * USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.                                           *
 *                                                                                                                    *
 **********************************************************************************************************************/

namespace System\Linq;

use \System\ArgumentNullException;
use \System\Collections\IEnumerable;
use \System\Collections\IItemContext;


/**
 * An ordered sequence.
 *
 * @package System\Linq
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class OrderedEnumerable extends Enumerable implements IOrderedEnumerable {
    /**
     * @var callable
     */
    private $_comparer;
    /**
     * @var callable
     */
    private $_selector;
    /**
     * @var IEnumerable
     */
    private $_sequence;



    /**
     * Initializes a new instance of that class.
     *
     * @param IEnumerable $sequence The base sequence.
     * @param callable $selector The selector for the sort values to use.
     * @param bool $preventKeys Prevent keys or not.
     */
    public function __construct(IEnumerable $sequence, callable $selector, callable $comparer) {
        $this->_comparer    = $comparer;
        $this->_selector    = $selector;
        $this->_sequence    = $sequence;

        parent::__construct($sequence);

        $this->resetMe(false);
    }


    /**
     * {@inheritDoc}
     */
    protected final function resetInner() {
        $this->resetMe();
    }

    /**
     * Resets that sequence.
     *
     * @param bool $resetSequence Reset inner sequence or not.
     */
    protected function resetMe(bool $resetSequence = true) {
        if ($resetSequence) {
            $this->_sequence->reset();
        }

        $selector = $this->_selector;

        // prepare items before ...
        $items = $this->_sequence
                      ->select(function($x, IItemContext $ctx) use ($selector) {
                                   $result         = new \stdClass();
                                   $result->key    = $ctx->key();
                                   $result->sortBy = $selector($x, $ctx);
                                   $result->value  = $x;

                                   return $result;
                               })
                      ->toArray();

        $comparer = $this->_comparer;

        \uasort($items,
                function(\stdClass $x, \stdClass $y) use ($comparer) : int {
                    return $comparer($x->sortBy, $y->sortBy);
                });

        $this->_i = static::createEnumerable($items)
                          ->withNewKeys('($key, $item) => $item->key')
                          ->select('$x => $x->value');
    }

    /**
     * {@inheritDoc}
     */
    public final function then($comparer = null) : IOrderedEnumerable {
        return $this->thenBy(true, $comparer);
    }

    /**
     * {@inheritDoc}
     */
    public final function thenBy($selector, $comparer = null) : IOrderedEnumerable {
        if (null === $selector) {
            throw new ArgumentNullException('selector');
        }

        if (true === $selector) {
            $selector = function($x) {
                return $x;
            };
        }

        $selector     = static::asCallable($selector);
        $thisSelector = $this->_selector;

        $comparer     = static::getComparerSafe($comparer);
        $thisComparer = $this->_comparer;

        return new static($this->_i,
                          function($x) use ($selector, $thisSelector) {
                              $result          = new \stdClass();
                              $result->level_0 = $thisSelector($x);  // first sort by this (level 0)
                              $result->level_1 = $selector($x);    // and then by this (level 1)

                              return $result;
                          },
                          function(\stdClass $x, \stdClass $y) use ($comparer, $thisComparer) : int {
                              $comp0 = $thisComparer($x->level_0, $y->level_0);
                              if (0 !== $comp0) {
                                  return $comp0;
                              }

                              $comp1 = $comparer($x->level_1, $y->level_1);
                              if (0 !== $comp1) {
                                  return $comp1;
                              }

                              return 0;
                          });
    }

    /**
     * {@inheritDoc}
     */
    public final function thenByDescending($selector, $comparer = null) : IOrderedEnumerable {
        $comparer = static::getComparerSafe($comparer);

        return $this->thenBy($selector,
                             function($x, $y) use ($comparer) : int {
                                 return $comparer($y, $x);
                             });
    }

    /**
     * {@inheritDoc}
     */
    public final function thenDescending($comparer = null) : IOrderedEnumerable {
        return $this->thenByDescending(true, $comparer);
    }
}
