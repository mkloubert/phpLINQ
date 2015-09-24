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
 * An object.
 *
 * @package System
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class Object implements IObject {
    /**
     * Object::toString()
     */
    public final function __toString() {
        return $this->toString()
                    ->getWrappedValue();
    }


    /**
     * Returns a value as callable.
     *
     * @param mixed $val The input value.
     *
     * @return callable|null The output value.
     *
     * @throws ArgumentException $val is invalid.
     */
    public static function asCallable($val) {
        if (\is_callable($val) || (null === $val)) {
            return $val;
        }

        return static::toLambda($val);
    }

    /**
     * {@inheritDoc}
     */
    public function equals($other) : bool {
        return $this == static::getRealValue($other);
    }

    /**
     * Extracts the "real" value if needed.
     *
     * @param mixed $val The input value.
     *
     * @return mixed The output value.
     */
    protected static function getRealValue($val) {
        while ($val instanceof IValueWrapper) {
            $val = $val->getWrappedValue();
        }

        return $val;
    }

    /**
     * {@inheritDoc}
     */
    public final function getType() : \ReflectionObject {
        return new \ReflectionObject($this);
    }

    /**
     * Checks if a value can be executed / is callable.
     *
     * @param mixed $val The value to check.
     *
     * @return bool Can be executed or not.
     */
    public static function isCallable($val) {
        return \is_callable($val) ||
               static::isLambda($val);
    }

    /**
     * Checks if a value is a valid lambda expression.
     *
     * @param mixed $val The value to check.
     *
     * @return bool Is valid lambda expression or not.
     */
    public static function isLambda($val) {
        return false !== static::toLambda($val, false);
    }

    /**
     * Creates a closure from a lambda expression.
     *
     * @param string $expr The expression.
     * @param bool $throwException Throw exception or return (false) instead.
     *
     * @return \Closure|bool The closure or (false) an error
     *
     * @throws ArgumentException $expr is no valid expression.
     * @throws FormatException Seems to be a lambda expression, but has an invalid format.
     */
    public static function toLambda($expr, bool $throwException = true) {
        $expr = \trim($expr);

        // check for lambda
        if (1 === \preg_match("/^(\\s*)([\\(]?)([^\\)]*)([\\)]?)(\\s*)(=>)/m", $expr, $lambdaMatches)) {
            if ((empty($lambdaMatches[2]) && !empty($lambdaMatches[4])) ||
                (!empty($lambdaMatches[2]) && empty($lambdaMatches[4])))
            {
                if ($throwException) {
                    throw new FormatException();
                }

                return false;
            }

            $lambdaBody = \trim(\substr($expr, \strlen($lambdaMatches[0])));

            while ((\strlen($lambdaBody) >= 2) &&
                   ('{' === \substr($lambdaBody, 0, 1)) && ('}' === \substr($lambdaBody, -1))) {

                $lambdaBody = \trim(\substr($lambdaBody, 1, \strlen($lambdaBody) - 2));
            }

            if ((';' !== \substr($lambdaBody, -1))) {
                $lambdaBody = \sprintf('return %s;',
                                       $lambdaBody);
            }

            if ('' === $lambdaBody) {
                $lambdaBody = 'return null;';
            }

            return eval(\sprintf('return function(%s) { %s };',
                                 $lambdaMatches[3], $lambdaBody));
        }

        if ($throwException) {
            throw new ArgumentException('expr');
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function toString() : IString {
        return new ClrString(\get_class($this));
    }

    /**
     * Wraps a predicate with a callable that requires a boolean as result value.
     *
     * @param callable $predicate The predicate to wrap.
     *
     * @return callable The wrapper.
     */
    public static function wrapPredicate($predicate) : callable {
        $predicate = static::asCallable($predicate);

        return function($x, $ctx) use ($predicate) : bool {
            return $predicate($x, $ctx);
        };
    }
}
