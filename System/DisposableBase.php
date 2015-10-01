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

namespace System;


/**
 * A basic disposable object.
 *
 * @package System
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
abstract class DisposableBase extends Object implements IDisposable {
    /**
     * @var bool
     */
    private $_isDisposed = false;


    /**
     * Frees the resources of that object.
     */
    final function __destruct() {
        $this->disposeInner(false);
    }


    /**
     * {@inheritDoc}
     */
    public final function dispose() {
        $this->disposeInner(true);
    }

    /**
     * Gets if that object has been disposed or not.
     *
     * @return bool Object has been disposed or not.
     */
    public final function isDisposed() : bool {
        return $this->_isDisposed;
    }

    private function disposeInner(bool $disposing) {
        if ($disposing && $this->isDisposed()) {
            // nothing more to do
            return;
        }

        $isDisposed = !$disposing ? $this->_isDisposed : true;
        $this->onDispose($disposing, $isDisposed);

        $this->_isDisposed = $isDisposed;
    }

    /**
     * The logic for the destructor and the DisposableBase::dispose() method.
     *
     * @param bool $disposing DisposableBase::dispose() method was called (true) or the
     *                        destructor (false).
     * @param bool &$isDisposed The new value for DisposableBase::isDisposed() method.
     *                          Is (true) by default if $disposing is also (true); otherwise it contains
     *                          the current value.
     */
    abstract protected function onDispose(bool $disposing, bool &$isDisposed = false);

    /**
     * Throws an exception if that object has been disposed.
     *
     * @throws ObjectDisposedException Object has been disposed.
     */
    protected final function throwIfDisposed() {
        if ($this->_isDisposed) {
            throw new ObjectDisposedException($this);
        }
    }
}
