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

namespace phpLINQ\Docs\Classes;

use \phpDocumentor\Reflection\DocBlock;
use \phpLINQ\Docs\Documentable;
use \phpLINQ\Docs\Language;
use \phpLINQ\Docs\Project;
use \phpLINQ\Docs\TemplateHandler;
use \phpLINQ\Docs\Type_;


abstract class Reflectable extends Documentable {
    use TemplateHandler;


    private $_reflector;


    protected function __construct(Project $proj, \Reflector $reflector, \SimpleXMLElement $xml) {
        $this->_reflector = $reflector;

        parent::__construct($proj, $xml);
    }


    /**
     * @return DocBlock
     */
    public function docBlock() {
        return new DocBlock($this->_reflector);
    }

    protected function findLinkForClass(\ReflectionClass $rc, Language $lang = null) {
        $result = null;

        $type = Type_::fromClass($rc, $lang, $this->project());
        if (null !== $type) {
            $result = $type->link();
        }

        return $result;
    }

    public function footer() {
        return $this->project()->footer();
    }

    public function header() {
        return $this->project()->header();
    }

    /**
     * @return \Reflector
     */
    public function reflector() {
        return $this->_reflector;
    }

    public function summary(Language $lang = null) {
        $doc = $this->docBlock();

        $summary = $this->findXmlNodeByLanguage('summary', null, $this->xml(), $lang);
        if (null === $summary) {
            $summary = $doc->getShortDescription();
        }

        $summary = \trim($summary);

        return '' !== $summary ? $summary : null;
    }
}
