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


/**
 * An item context with an index.
 *
 * @package System\Collections
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class IndexedItemContext extends ItemContext implements IIndexedItemContext {
    /**
     * @var int
     */
    private $_index;
    /**
     * @var bool|null
     */
    private $_isLast;


    /**
     * Initializes a new instance of that class.
     *
     * @param IEnumerable $seq The underlying sequence.
     * @param int $index The zero based index.
     * @param bool $invokeNext Invoke IEnumerable::next() method of $seq or not.
     * @param mixed $previousValue The value that was set via IItemContext::nextValue() in the context of
     *                             the previous item.
     */
    public function __construct(IEnumerable $seq, int $index, bool $invokeNext = true, $previousValue = null) {
        $this->_index = $index;

        parent::__construct($seq, $previousValue);

        if ($invokeNext) {
            $seq->next();

            $this->_isLast = !$seq->valid();
        }
    }


    /**
     * {@inheritDoc}
     */
    public final function index() : int {
        return $this->_index;
    }

    /**
     * {@inheritDoc}
     */
    public final function isFirst() : bool {
        return 0 === $this->_index;
    }

    /**
     * {@inheritDoc}
     */
    public final function isLast() {
        return $this->_isLast;
    }
}
