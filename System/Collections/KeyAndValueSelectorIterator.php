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

namespace System\Collections;

use \System\ArgumentNullException;
use \System\Object;


/**
 * Iterator that wraps a sequence by using a selector
 * that produces new keys AND values.
 *
 * @package System\Collections
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class KeyAndValueSelectorIterator extends KeySelectorIterator {
    /**
     * @var callable
     */
    private $_valueSelector;


    /**
     * Initializes a new instance of that class.
     *
     * @param IEnumerable $seq The inner sequence.
     * @param callable $keySelector The key selector to use.
     * @param callable $valueSelector The value / item selector to use.
     *
     * @throws ArgumentNullException $keySelector is (null).
     */
    public function __construct(IEnumerable $seq, $keySelector, $valueSelector) {
        if (null === $valueSelector) {
            throw new ArgumentNullException('valueSelector');
        }

        $this->_valueSelector = Object::asCallable($valueSelector);

        parent::__construct($seq, $keySelector);
    }


    /**
     * {@inheritDoc}
     */
    public final function createNewFromSequence(IEnumerable $newSeq) {
        return new static($newSeq, $this->keySelector(), $this->_valueSelector);
    }

    /**
     * {@inheritDoc}
     */
    public final function current() {
        $ctx = new ItemContext($this->sequence());

        return \call_user_func($this->_valueSelector,
                               $ctx->item(), $ctx->key(), $ctx, $this);
    }

    /**
     * Gets the value selector.
     *
     * @return callable The value selector.
     */
    public final function valueSelector() : callable {
        return $this->_valueSelector;
    }
}
