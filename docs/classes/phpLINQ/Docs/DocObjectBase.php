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


abstract class DocObjectBase {
    protected function findXmlNodeByLanguage(
        $nodeName,
        $defXml = null,
        \SimpleXMLElement $parentNode = null,
        Language $lang = null)
    {
        $nodeName = \trim($nodeName);
        if ('' === $nodeName) {
            $nodeName = null;
        }

        $langId = null;
        if (null !== $lang) {
            $langId = $lang->id();
        }

        $result = null;

        if (null !== $parentNode) {
            foreach ($parentNode->children() as $childNode) {
                if (!$childNode instanceof \SimpleXMLElement) {
                    continue;
                }

                if ($nodeName !== $childNode->getName()) {
                    continue;
                }

                $nodeLang = Language::normalizeId($childNode['lang']);
                if ($nodeLang !== $langId) {
                    if (null !== $nodeLang) {
                        continue;
                    }
                }

                $result = $childNode;
            }

            if (null === $result) {
                if (!$defXml instanceof \SimpleXMLElement) {
                    $defXml = \trim($defXml);
                    if ('' !== $defXml) {
                        $result = \simplexml_load_string($defXml);
                    }
                }
                else {
                    $result = $defXml;
                }
            }
        }

        return $result;
    }

    protected function parseForHtmlOutput($str) {
        $str = \strval($str);

        $str = \str_ireplace("\t", '    ', $str);
        $str = \str_ireplace("\r", ''    , $str);

        return \htmlentities($str);
    }

    protected function parseNamespace($ns) {
        $ns = \trim($ns);

        while (0 === stripos($ns, "\\")) {
            $ns = \trim(\substr($ns, 1));
        }

        while ((strlen($ns) > 0) &&
               ("\\" === \substr($ns, -1))) {

            $ns = \trim(\substr($ns, 0, \strlen($ns) - 1));
        }

        if ('' !== $ns) {
            $ns = \str_ireplace("\\", '.', $ns);
        }

        return '' !== $ns ? $ns : null;
    }
}

