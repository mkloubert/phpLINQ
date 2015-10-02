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

namespace phpLINQ\Docs;

use \phpDocumentor\Reflection\DocBlock\Tag\ReturnTag;
use \System\Linq\Enumerable;


class Type_ {
    /**
     * @var string
     */
    protected $_link;
    /**
     * @var string
     */
    protected $_name;
    /**
     * @var \ReflectionClass
     */
    protected $_reflector;


    public static function fromClass(\ReflectionClass $rc, Language $lang = null, Project $proj = null) {
        return static::fromName($rc->getName(), $lang, $proj);
    }

    /**
     * @return static|null
     */
    public static function fromName($name, Language $lang = null, Project $proj = null) {
        $result = null;

        $name = \trim($name);
        if ('' !== $name) {
            $result = new static();
            $result->_name = $name;

            if (\interface_exists($name) ||
                \trait_exists($name) ||
                \class_exists($name)) {

                $result->_reflector = new \ReflectionClass($name);

                if (!$result->_reflector->isUserDefined()) {
                    // PHP type

                    $phpLang = 'en';
                    if (null !== $lang) {
                        $phpLang = $lang->id();
                    }

                    $result->_link = \sprintf('http://php.net/manual/%s/class.%s.php',
                                              $phpLang,
                                              \trim(\strtolower(\str_ireplace("\\", '.', $result->_reflector->getName()))));
                }
                else {
                    if (null !== $proj) {
                        foreach ($proj->classes() as $cls) {
                            if ($cls->reflector()->getName() === $result->_reflector->getName()) {
                                $result->_link = $cls->getDocumentFilename($lang);
                            }
                        }
                    }
                }
            }
            else {
                switch ($name) {
                    case 'callable':
                        $result->_link = 'https://github.com/mkloubert/phpLINQ/wiki/Callable';
                        break;
                }
            }
        }
        else {
            $result = null;
        }

        return $result;
    }

    /**
     * @return static[]
     */
    public static function fromNameArray($names, Language $lang = null, Project $proj = null) {
        $rc = new \ReflectionClass(static::class);
        $fn = $rc->getMethod('fromName')->getClosure(null);

        return Enumerable::create($names)
                         ->select('$x => \trim($x)')
                         ->where('$x => "" !== $x')
                         ->distinct('($x) => \strtolower($x)')
                         ->select(function($x) use ($fn, $lang, $proj) {
                                      return $fn($x, $lang, $proj);
                                  })
                         ->ofType($rc)
                         ->toArray();
    }

    /**
     * @return static[]
     */
    public static function fromReturnTag(ReturnTag $tag, Language $lang = null, Project $proj = null) {
        return static::fromNameArray($tag->getTypes(), $lang, $proj);
    }

    /**
     * @return string|null
     */
    public function link() {
        return $this->_link;
    }

    /**
     * @return string
     */
    public function name() {
        return $this->_name;
    }

    /**
     * @return \ReflectionClass|null
     */
    public function reflector(\ReflectionClass $newValue = null) {
        return $this->_reflector;
    }
}
