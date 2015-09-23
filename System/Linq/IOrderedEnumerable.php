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

namespace System\Linq;

use \System\ArgumentException;
use \System\ArgumentNullException;
use \System\Collections\IEnumerable;


/**
 * Describes an ordered sequence.
 *
 * @package System\Collections
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
interface IOrderedEnumerable extends IEnumerable {
    /**
     * Performs a sub ordering on the current sequence by using the items as sort values.
     *
     * @param callable $comparer The custom comparer to use.
     *                           If there is only one argument and this argument is a boolean it is used as value for
     *                           $preventKeys.
     * @param bool|null $preventKeys Prevent keys or not.
     *                               (null) indicates to use the (default) value from that sequence.
     *
     * @return IOrderedEnumerable The new sequence.
     *
     * @throws ArgumentException $comparer is no valid callable / lambda expression.
     */
    function then($comparer = null, $preventKeys = null) : IOrderedEnumerable;

    /**
     * Performs a sub ordering on the current sequence.
     *
     * @param callable|bool $selector The selector for the sort values.
     *                                (true) indicates to use the items itself as sort values.
     * @param callable $comparer The custom comparer to use.
     *                           If there are only two arguments and this argument is a boolean it is used as value for
     *                           $preventKeys.
     * @param bool|null $preventKeys Prevent keys or not.
     *                               (null) indicates to use the (default) value from that sequence.
     *
     * @return IOrderedEnumerable The new sequence.
     *
     * @throws ArgumentException $selector / $comparer is no valid callable / lambda expression.
     * @throws ArgumentNullException $selector is (null).
     */
    function thenBy($selector, $comparer = null, $preventKeys = null) : IOrderedEnumerable;

    /**
     * Performs a descending sub ordering on the current sequence.
     *
     * @param callable|bool $selector The selector for the sort values.
     *                                (true) indicates to use the items itself as sort values.
     * @param callable $comparer The custom comparer to use.
     *                           If there are only two arguments and this argument is a boolean it is used as value for
     *                           $preventKeys.
     * @param bool|null $preventKeys Prevent keys or not.
     *                               (null) indicates to use the (default) value from that sequence.
     *
     * @return IOrderedEnumerable The new sequence.
     *
     * @throws ArgumentException $selector / $comparer is no valid callable / lambda expression.
     * @throws ArgumentNullException $selector is (null).
     */
    function thenByDescending($selector, $comparer = null, $preventKeys = null) : IOrderedEnumerable;

    /**
     * Performs a descending sub ordering on the current sequence by using the items as sort values.
     *
     * @param callable $comparer The custom comparer to use.
     *                           If there is only one argument and this argument is a boolean it is used as value for
     *                           $preventKeys.
     * @param bool|null $preventKeys Prevent keys or not.
     *                               (null) indicates to use the (default) value from that sequence.
     *
     * @return IOrderedEnumerable The new sequence.
     *
     * @throws ArgumentException $comparer is no valid callable / lambda expression.
     */
    function thenDescending($comparer = null, $preventKeys = null) : IOrderedEnumerable;
}
