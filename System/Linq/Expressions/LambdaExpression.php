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

namespace System\Linq\Expressions;

use \System\ArgumentException;
use \System\ClrString;
use \System\IString;


/**
 * A lambda expression that can be invoked.
 *
 * @package System\Linq\Expressions
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class LambdaExpression extends \System\Object {
    /**
     * @var \Closure
     */
    private $_closure;
    /**
     * @var string
     */
    private $_expr;


    /**
     * Initializes a new instance of that class.
     *
     * @param string $expr The lambda expression.
     *
     * @throws ArgumentException $expr is no (valid) lambda expression.
     */
    public function __construct($expr) {
        $this->_expr = \trim($expr);

        $this->_closure = static::toLambda($this->_expr);
    }

    /**
     * Invokes the expression.
     *
     * @param mixed ...$arg One or more argument for the invocation.
     *
     * @return mixed The result of the invocation.
     */
    public final function __invoke() {
        return \call_user_func_array($this->_closure,
                                     \func_get_args());
    }

    /**
     * Gets the underlying closure.
     *
     * @return \Closure The underlying closure.
     */
    public final function getClosure() : \Closure {
        return $this->_closure;
    }

    /**
     * Gets the reflector of the underlying closure.
     *
     * @return \ReflectionFunction The reflector.
     */
    public final function getReflector() : \ReflectionFunction {
        return new \ReflectionFunction($this->_closure);
    }

    /**
     * {@inheritDoc}
     */
    public function toString() : IString {
        return new ClrString($this->_expr);
    }
}
