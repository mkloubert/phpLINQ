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
 * An iterator for IDictionaryEntry instances.
 *
 * @package System\Collections
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class DictionaryEntryIterator implements \Iterator {
    /**
     * @var \Iterator
     */
    private $_i;


    /**
     * Initializes a new instance of that class.
     *
     * @param \Iterator $i The iterator that contains the dictionary entries.
     */
    public function __construct(\Iterator $i) {
        $this->_i = $i;
    }


    /**
     * {@inheritDoc}
     */
    public final function current() {
        return $this->currentEntry()
                    ->value();
    }

    /**
     * Gets the current dictionary entry instance.
     *
     * @return IDictionaryEntry The current dictionary entry.
     */
    protected function currentEntry() : IDictionaryEntry {
        return $this->_i->current();
    }

    /**
     * {@inheritDoc}
     */
    public final function key() {
        return $this->currentEntry()
                    ->key();
    }

    /**
     * {@inheritDoc}
     */
    public final function rewind() {
        $this->_i->rewind();
    }

    /**
     * {@inheritDoc}
     */
    public final function next() {
        $this->_i->next();
    }

    /**
     * {@inheritDoc}
     */
    public final function valid() {
        return $this->_i->valid();
    }
}
