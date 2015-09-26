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
 * Iterator that wraps a sequence by using a selector that produces new keys.
 *
 * @package System\Collections
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class KeySelectorIterator implements \Iterator {
    /**
     * @var callable
     */
    private $_keySelector;
    /**
     * @var IEnumerable
     */
    private $_seq;


    /**
     * Initializes a new instance of that class.
     *
     * @param IEnumerable $seq The inner sequence.
     * @param callable $keySelector The key selector to use.
     *
     * @throws ArgumentNullException $keySelector is (null).
     */
    public function __construct(IEnumerable $seq, $keySelector) {
        if (null === $keySelector) {
            throw new ArgumentNullException('keySelector');
        }

        $this->_keySelector = Object::asCallable($keySelector);
        $this->_seq         = $seq;
    }


    /**
     * Creates a new instance with the same key selector but with another sequence.
     *
     * @param IEnumerable $newSeq The new sequence.
     *
     * @return KeySelectorIterator The new instance.
     */
    public final function createNewFromSequence(IEnumerable $newSeq) : KeySelectorIterator {
        return new static($newSeq, $this->_keySelector);
    }

    /**
     * {@inheritDoc}
     */
    public final function current() {
        return $this->_seq
                    ->current();
    }

    /**
     * {@inheritDoc}
     */
    public final function key() {
        $ctx = new ItemContext($this->_seq);

        return \call_user_func($this->_keySelector,
                               $ctx->key(), $ctx->item(), $ctx);
    }

    /**
     * Gets the key selector.
     *
     * @return callable The key selector.
     */
    public final function keySelector() {
        return $this->_keySelector;
    }

    /**
     * {@inheritDoc}
     */
    public final function next() {
        $this->_seq->next();
    }

    /**
     * {@inheritDoc}
     */
    public final function rewind() {
        $this->_seq->rewind();
    }

    /**
     * Gets the underlying sequence.
     *
     * @return IEnumerable The underlying sequence.
     */
    public final function sequence() : IEnumerable {
        return $this->_seq;
    }

    /**
     * {@inheritDoc}
     */
    public final function valid() {
        return $this->_seq->valid();
    }
}
