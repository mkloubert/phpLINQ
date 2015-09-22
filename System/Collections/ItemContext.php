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

use \System\Object;


/**
 * An item context.
 *
 * @package System\Collections
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class ItemContext extends Object implements IItemContext {
    private $_item;
    private $_key;
    private $_nextValue;
    private $_previousValue;
    /**
     * @var IEnumerable
     */
    private $_seq;
    private $_value;


    /**
     * Initializes a new instance of that class.
     *
     * @param mixed $previousValue The value that was set via IItemContext::nextValue() in the context of
     *                             the previous item.
     *
     * @param IEnumerable $seq The underlying sequence.
     */
    public function __construct(IEnumerable $seq, $previousValue = null) {
        $this->_seq           = $seq;
        $this->_previousValue = $previousValue;

        $this->_key  = $this->_seq->key();
        $this->_item = $this->_seq->current();
    }


    /**
     * {@inheritDoc}
     */
    public final function item() {
        return $this->_item;
    }

    /**
     * {@inheritDoc}
     */
    public final function key() {
        return $this->_key;
    }

    /**
     * {@inheritDoc}
     */
    public final function previousValue() {
        return $this->_previousValue;
    }

    /**
     * {@inheritDoc}
     */
    public final function nextValue($newValue = null) {
        if (\func_num_args() > 0) {
            $this->_nextValue = $newValue;
        }

        return $this->_nextValue;
    }

    /**
     * {@inheritDoc}
     */
    public final function sequence() : IEnumerable {
        return $this->_seq;
    }

    /**
     * {@inheritDoc}
     */
    public final function value($newValue = null) {
        if (\func_num_args() > 0) {
            $this->_value = $newValue;
        }

        return $this->_value;
    }
}
