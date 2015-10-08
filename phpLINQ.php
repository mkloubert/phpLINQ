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

// DON'T REMOVE THE "UNUSED" NAMESPACES!!!
// This is for better access in lambda expressions, e.g.
use \System\ArgumentException;
use \System\ClrString;
use \System\Linq\Enumerable;


/**
 * Provides services in a root namespace context.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class phpLINQ {
    /**
     * Executes code globally.
     *
     * @param string $code The code to execute.
     *
     * @return mixed The result of the execution.
     *
     * @throws \System\InvalidCastException $code cannot be a string.
     */
    public static final function execGlobal($code) {
        return eval(\System\ClrString::valueToString($code, false));
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
     * @param object $bindTo Custom object to bind the closure to.
     *
     * @return \Closure|bool The closure or (false) on error.
     *
     * @throws ArgumentException $expr is no valid expression.
     *                           -- or --
     *                           $bindTo is no valid object.
     */
    public static function toLambda($expr, bool $throwException = true, $bindTo = null) {
        if (null !== $bindTo) {
            if (!is_object($bindTo) && (false !== $bindTo)) {
                throw new ArgumentException('bindTo', 'No valid object!', null, 2);
            }
        }

        $throwOrReturn = function() use ($throwException) {
            if ($throwException) {
                throw new ArgumentException('expr', 'No lambda expression!', null, 0);
            }

            return false;
        };

        if (!ClrString::canBeString($expr)) {
            return $throwOrReturn();
        }

        $expr = trim(ClrString::valueToString($expr));

        // check for lambda
        if (1 === preg_match("/^(\\s*)([\\(]?)([^\\)]*)([\\)]?)(\\s*)(=>)/m", $expr, $lambdaMatches)) {
            if ((empty($lambdaMatches[2]) && !empty($lambdaMatches[4])) ||
                (!empty($lambdaMatches[2]) && empty($lambdaMatches[4])))
            {
                if ($throwException) {
                    throw new ArgumentException('expr', 'Syntax error in lambda expression!', null, 1);
                }

                return false;
            }

            $lambdaBody = trim(substr($expr, strlen($lambdaMatches[0])),  // take anything after =>
                               '{}' . " \t\n\r\0\x0B");  // remove surrounding {}

            if ('' !== $lambdaBody) {
                if ((';' !== \substr($lambdaBody, -1))) {
                    // auto add return statement
                    $lambdaBody = 'return ' . $lambdaBody . ';';
                }
            }

            /* @var Closure $result */

            // build closure
            $result = self::execGlobal('return function(' . $lambdaMatches[3] . ') { ' . $lambdaBody . ' };');

            if (false !== $bindTo) {
                $obj = new self();
                $result->bindTo($bindTo ?? $obj);
            }

            return $result;
        }

        return $throwOrReturn();
    }
}
