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

use \System\Linq\Enumerable;


/**
 * A basic collection that uses an array for handling its items.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 * @package System\Collections
 */
abstract class ArrayCollectionBase extends Enumerable {
    /**
     * @var array
     */
    protected $_items;
    /**
     * @var int
     */
    private $_key = 0;


    /**
     * {@inheritDoc}
     */
    public final function count() {
        return \count($this->_items);
    }

    /**
     * {@inheritDoc}
     */
    public function current() {
        $result = $this->_items[$this->_key];
        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function elementAtOrDefault(int $index, $defValue = null, &$found = false) {
        if (\array_key_exists($index, $this->_items)) {
            return $this->_items[$index];
        }

        return $defValue;
    }

    /**
     * Returns the value that is used to represent an EOF key.
     *
     * @return mixed The EOF key.
     */
    protected function getEOFKey() {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function key() {
        return $this->valid() ? $this->_key
                              : $this->getEOFKey();
    }

    /**
     * {@inheritDoc}
     */
    public final function next() {
        if (!$this->valid()) {
            $this->throwException('No more items available!');
        }

        ++$this->_key;
    }

    /**
     * {@inheritDoc}
     */
    protected final function resetInner() {
        $this->_key = 0;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize() {
        return \serialize($this->_items);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized) {
        $this->_items = \unserialize($serialized) ?? [];
    }

    /**
     * {@inheritDoc}
     */
    public final function valid() {
        return $this->_key < \count($this->_items);
    }
}
