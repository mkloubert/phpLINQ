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
    use DocumentGenerator;
    use FileWriter;
    use LanguageHandler;
    use OutputDirectoryHandler;
    use TemplateHandler;
    use XmlObject;


    /**
     * @var string
     */
    private $_baseDir;
    /**
     * @var Class_[]
     */
    private $_classes;
    /**
     * @var string
     */
    private $_outDir;


    protected function __construct(\SimpleXMLElement $xml) {
        $this->_xml = $xml;
    }


    public function baseDir() {
        if (\func_num_args() > 0) {
            $newValue = \func_get_arg(0);

            $this->_baseDir = realpath($newValue);
        }

        return $this->_baseDir;
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
                                !\class_exists($className) &&
                                !trait_exists($className)) {

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

    public function generate() {
        // CSS
        $this->copyFile('template/css/normalize.css', './css');
        $this->copyFile('template/css/foundation.min.css', './css');
        $this->copyFile('template/css/highlight/default.css', './css/highlight');
        $this->copyFile('template/css/highlight/github.css', './css/highlight');

        // highlight.pack.js

        // Javascript
        $this->copyFile('template/js/foundation.min.js', './js');
        $this->copyFile('template/js/highlight.pack.js', './js');
        $this->copyFile('template/js/vendor/modernizr.js', './js/vendor');
        $this->copyFile('template/js/vendor/jquery.js', './js/vendor');

        foreach ($this->languages() as $lang) {
            $this->generateDocumentation($lang);
        }
    }

    public function generateDocumentation(Language $lang = null) {
        $this->generateIndexFile($lang);

        foreach ($this->classes() as $cls) {
            $cls->generateDocumentation($lang);
        }
    }

    protected function generateIndexFile(Language $lang = null) {
        $file = $this->createNewFile($this->getDocumentFilename($lang));
        if (!\is_resource($file)) {
            return false;
        }

        $me = $this;

        $classes = $this->classes();

        $header = $this->header();
        $footer = $this->footer();

        $this->writeTo($file, function() use (&$classes, $footer, $header, $lang, $me) {
            $classes = \array_map(function(Class_ $c) use ($me) {
                $result              = [];
                $result['class']     = $c;
                $result['name']      = \trim($c->reflector()->getShortName());
                $result['namespace'] = $me->parseNamespace($c->reflector()->getNamespaceName());
                $result['sortBy']    = \trim($c->reflector()->getName());

                return $result;
            }, $classes);

            \usort($classes, function(array $x, array $y) {
                return \strcasecmp($x['sortBy'], $y['sortBy']);
            });

            echo $header;

            echo '<div class="row">';

            echo '<h1>phpLINQ Documentation</h1>';

            if (!empty($classes)) {
                echo '<h2>Types</h2>';

                $addTableFooter = false;
                $doAddTableFooterIfNeeded = function() use (&$addTableFooter) {
                    if (!$addTableFooter) {
                        return;
                    }

                    echo '</tbody>';
                    echo '</table>';
                };

                $currentNamespace = null;
                foreach ($classes as $cls) {
                    /* @var Class_ $clsObj */
                    $clsObj = $cls['class'];

                    if ($cls['namespace'] !== $currentNamespace) {
                        $doAddTableFooterIfNeeded();

                        $addTableFooter = true;

                        echo '<h3>' . \htmlentities($cls['namespace']) . ' </h3>';

                        echo '<table style="width: 100%;">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th>Name</th>';
                        echo '<th>Description</th>';
                        echo '</tr>';
                        echo '</thead>';

                        echo '<tbody>';
                    }

                    $classFile = $clsObj->getDocumentFilename($lang);

                    $summary = $clsObj->summary($lang);

                    echo '<tr>';
                    echo '<td>';
                    echo '<a href="' . \htmlspecialchars($classFile) . '">';
                    echo \htmlentities($cls['name']);
                    echo '</a>';
                    echo '</td>';
                    echo '<td>' . \htmlentities($summary) .'</td>';
                    echo '</tr>';

                    $currentNamespace = $cls['namespace'];
                }

                $doAddTableFooterIfNeeded();
            }

            echo '</div>';

            echo $footer;
        });

        return true;
    }

    public function getDocumentFilename(Language $lang = null) {
        return \sprintf('index.%s.html',
                        $lang->id());
    }

    public function languages() : array {
        $result = [];

        if ($this->xml()->languages) {
            foreach ($this->xml()->languages as $languagesNode) {
                if ($languagesNode->language) {
                    foreach ($languagesNode->language as $languageNode) {
                        $result[] = new Language($languageNode);
                    }
                }
            }
        }

        if (empty($result)) {
            $result[] = \simplexml_load_string('<language>%s</language>',
                                               \htmlentities($this->defaultLanguage()));
        }

        return $result;
    }

    public function outDir() {
        if (\func_num_args() > 0) {
            $newValue = \func_get_arg(0);

            $this->_outDir = realpath($newValue);
        }

        return $this->_outDir;
    }
}
