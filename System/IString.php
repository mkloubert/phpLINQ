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

use \System\Collections\IEnumerable;


/**
 * Describes a string.
 *
 * @package System
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
interface IString extends \ArrayAccess, IComparable, IEnumerable, IValueWrapper {
    /**
     * Returns a formatted string by using that instance as format string.
     *
     * @param mixed ...$arg One or more argument for the format string.
     *
     * @return IString The formatted string.
     */
    function __invoke();


    /**
     * Returns that string as mutable version.
     *
     * @return IString Mutable string.
     */
    function asMutable() : IString;

    /**
     * {@inheritDoc}
     */
    function getWrappedValue() : string;

    /**
     * Gets if the string is mutable or not.
     *
     * @return bool Is mutable or not.
     */
    function isMutable() : bool;

    /**
     * Checks if that string contains whitespaces only.
     *
     * @param string $character_mask The custom list of whitespace characters to use.
     *
     * @return bool Contains whitespaces only or not.
     */
    function isWhitespace($character_mask = null) : bool;

    /**
     * Gets the length of the string.
     *
     * @return int The length.
     */
    function length() : int;

    /**
     * Converts the string to a char array.
     *
     * @return string[] The string as char array.
     */
    function toCharArray() : array;

    /**
     * Returns a version of that string with lowercase chars.
     *
     * @return IString The (new) string.
     */
    function toLower() : IString;

    /**
     * Returns a version of that string with uppercase chars.
     *
     * @return IString The (new) string.
     */
    function toUpper() : IString;

    /**
     * Strip whitespace (or other characters) from the beginning and end of the string.
     *
     * @param string $character_mask The custom list of characters to strip.
     *
     * @return IString The (new) string.
     */
    function trim($character_mask = null) : IString;

    /**
     * Strip whitespace (or other characters) from the end of the string.
     *
     * @param string $character_mask The custom list of characters to strip.
     *
     * @return IString The (new) string.
     */
    function trimEnd($character_mask = null) : IString;

    /**
     * Strip whitespace (or other characters) from the beginning of the string.
     *
     * @param string $character_mask The custom list of characters to strip.
     *
     * @return IString The (new) string.
     */
    function trimStart($character_mask = null) : IString;
}
