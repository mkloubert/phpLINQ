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

use \phpLINQ\Docs\Classes\Class_;


class Project extends DocObjectBase {
    /**
     * @var Class_[]
     */
    private $_classes;
    /**
     * @var \SimpleXMLElement
     */
    private $_xml;


    protected function __construct(\SimpleXMLElement $xml) {
        $this->_xml = $xml;
    }


    /**
     * @return Class_[]
     */
    public function classes() {
        if (null === $this->_classes) {
            $classList = [];

            if ($this->xml()->classes) {
                foreach ($this->xml()->classes as $classesNode) {
                    if ($classesNode->class) {
                        foreach ($classesNode->class as $classNode) {
                            $className = \trim($classNode['name']);
                            $className = \str_ireplace('.', "\\", $className);

                            while (0 === stripos($className, "\\")) {
                                $className = trim(substr($className, 1));
                            }

                            while ((strlen($className) > 0) &&
                                ("\\" == substr($className, -1))) {

                                $className = trim(substr($className, 0, strlen($className) - 1));
                            }

                            if ('' === $className) {
                                continue;
                            }

                            $className = "\\" . $className;

                            if (!\interface_exists($className) &&
                                !\class_exists($className)) {

                                continue;
                            }

                            $classList[] = new Class_($this,
                                                      new \ReflectionClass($className),
                                                      $classNode);
                        }
                    }
                }
            }

            usort($classList, function(Class_ $x, Class_ $y) {
                return \strcasecmp($x->reflector()->getName(),
                                   $y->reflector()->getName());
            });

            $this->_classes = $classList;
        }

        return $this->_classes;
    }

    public static function fromXml(\SimpleXMLElement $xml = null) {
        if (null === $xml) {
            $xml = \simplexml_load_string('<documentation />');
        }

        return new static($xml);
    }

    public static function fromXmlFile($file) {
        $file = \realpath($file);
        if (false === $file) {
            return null;
        }

        $result = @simplexml_load_file($file);
        if (false === $result) {
            return false;
        }

        return static::fromXml($result);
    }

    public function generateDocumentation() {

    }

    /**
     * @return \SimpleXMLElement
     */
    public function xml() {
        return $this->_xml;
    }
}
