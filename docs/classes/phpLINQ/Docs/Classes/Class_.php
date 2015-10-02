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

use \phpLINQ\Docs\Language;
use \phpLINQ\Docs\Project;
use \phpLINQ\Docs\SyntaxHandler;
use \System\Collections\IEachItemContext;
use \System\Linq\Enumerable;


class Class_ extends Reflectable {
    use SyntaxHandler;


    /**
     * @var Method_[]
     */
    private $_methods;


    public function __construct(Project $proj, \ReflectionClass $reflector, \SimpleXMLElement $xml) {
        parent::__construct($proj, $reflector, $xml);
    }


    protected function classType(Language $lang = null) {
        $result = 'class';
        if ($this->reflector()->isInterface()) {
            $result =  'interface';
        }
        else if ($this->reflector()->isTrait()) {
            $result = 'trait';
        }

        return $result;
    }

    public function generateDocumentation(Language $lang = null) {
        $this->generateIndexFile($lang);

        foreach ($this->methods() as $method) {
            $method->generateDocumentation($lang);
        }
    }

    protected function generateIndexFile(Language $lang = null) {
        $file = $this->createNewFile($this->getDocumentFilename($lang));
        if (!\is_resource($file)) {
            return false;
        }

        $methods = $this->methods();

        $header = $this->header();
        $footer = $this->footer();

        $me = $this;

        $this->writeTo($file, function() use ($footer, $header, $lang, $me, $methods) {
            $methods = \array_map(function(Method_ $m) {
                $result           = [];
                $result['method'] = $m;
                $result['name']   = \trim($m->reflector()->getName());

                return $result;
            }, $methods);

            $classType = $this->classType($lang);

            $inheritFrom = Enumerable::create(\array_merge($me->reflector()->getInterfaces(),
                                                           [$me->reflector()->getParentClass()],
                                                           $me->reflector()->getTraits()))
                                     ->ofType(\ReflectionClass::class)
                                     ->orderBy(function(\ReflectionClass $rc) {
                                                   return $rc->getName();
                                               }, "\\strcasecmp")
                                     ->asResettable();

            /* @var \ReflectionClass[] $classesAndInterfaces */
            $classesAndInterfaces = $inheritFrom->reset()
                                                ->where(function(\ReflectionClass $rc) {
                                                            return !$rc->isTrait();
                                                        })
                                                ->toArray();

            /* @var \ReflectionClass[] $traits */
            $traits = $inheritFrom->reset()
                                  ->where(function(\ReflectionClass $rc) {
                                              return $rc->isTrait();
                                          })
                                  ->toArray();

            echo $header;

            echo '<div class="row">';

            echo '<ul class="breadcrumbs">';
            echo '<li>';
            echo '<a href="' . \htmlspecialchars($this->project()->getDocumentFilename($lang)) . '">Home</a>';
            echo '</li>';
            echo '<li class="unavailable">';
            echo '<a href="#">';
            echo \htmlentities($me->parseNamespace($me->reflector()->getNamespaceName()));
            echo '</a>';
            echo '</li>';
            echo '<li class="current">';
            echo '<a href="#">';
            echo \htmlentities($me->reflector()->getShortName());
            echo '</a>';
            echo '</li>';
            echo '</ul>';

            echo '<h1>';
            echo \htmlentities($me->reflector()->getShortName()) . ' ' . $classType;
            echo '</h1>';

            echo '<h2>Syntax</h2>';

            $syntax = $this->syntax($lang);

            echo '<pre style="background-color: transparent;">';
            echo '<code class="php">';
            echo $me->parseForHtmlOutput($syntax);
            echo '</code>';
            echo '</pre>';

            if (!empty($classesAndInterfaces)) {
                echo '<h2>Inherited from</h2>';

                echo '<table style="width: 100%;">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Name</th>';
                echo '<th>Namespace</th>';
                echo '</tr>';
                echo '</thead>';

                echo '<tbody>';

                foreach ($classesAndInterfaces as $rc) {
                    $rcClassLink = \trim($me->findLinkForClass($rc, $lang));

                    echo '<tr>';
                    echo '<td>';

                    if ('' !== $rcClassLink) {
                        echo '<a href="' . \htmlspecialchars($rcClassLink) . '">';
                    }

                    echo \htmlentities($rc->getShortName());

                    if ('' !== $rcClassLink) {
                        echo '</a>';
                    }

                    echo '</td>';

                    echo '<td>' . \htmlentities($me->parseNamespace($rc->getNamespaceName())) . '</td>';

                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
            }

            if (!empty($traits)) {
                echo '<h2>Uses</h2>';

                echo '<table style="width: 100%;">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Name</th>';
                echo '<th>Namespace</th>';
                echo '</tr>';
                echo '</thead>';

                echo '<tbody>';

                foreach ($traits as $rc) {
                    $rcClassLink = \trim($me->findLinkForClass($rc, $lang));

                    echo '<tr>';
                    echo '<td>';

                    if ('' !== $rcClassLink) {
                        echo '<a href="' . \htmlspecialchars($rcClassLink) . '">';
                    }

                    echo \htmlentities($rc->getShortName());

                    if ('' !== $rcClassLink) {
                        echo '</a>';
                    }

                    echo '</td>';

                    echo '<td>' . \htmlentities($me->parseNamespace($rc->getNamespaceName())) . '</td>';

                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
            }

            if (!empty($methods)) {
                echo '<h2>Methods</h2>';

                echo '<table style="width: 100%;">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Name</th>';
                echo '<th>Description</th>';
                echo '</tr>';
                echo '</thead>';

                echo '<tbody>';

                foreach ($methods as $m) {
                    /* @var Method_ $methodObj */
                    $methodObj = $m['method'];

                    $methodFile = $methodObj->getDocumentFilename($lang);

                    $summary = $methodObj->summary($lang);

                    echo '<tr>';
                    echo '<td>';
                    echo '<a href="' . \htmlspecialchars($methodFile) . '">';
                    echo \htmlentities($m['name']);
                    echo '</a>';
                    echo '</td>';
                    echo '<td>' . \htmlentities($summary) .'</td>';
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
            }

            echo '</div>';

            echo <<<'EOT'
<script type="text/javascript">

    jQuery(document).ready(function() {
        hljs.initHighlighting();
    });

</script>
EOT;


            echo $footer;
        });

        foreach ($methods as $m) {
            $m->generateDocumentation($lang);
        }
    }

    public function getDocumentFilename(Language $lang = null) {
        $className = $this->reflector()->getName();
        $className = \str_ireplace("\\", '.', $className);

        return \sprintf('%s.class.%s.html',
                        $lang->id(), $className);
    }

    /**
     * @return Method_[]
     */
    public function methods() {
        if (null === $this->_methods) {
            $methodList = [];

            foreach ($this->reflector()->getMethods() as $method) {
                if ($method->getDeclaringClass()->getName() !== $this->reflector()->getName()) {
                    continue;
                }

                if ($method->isPrivate()) {
                    continue;
                }

                if ($method->getDeclaringClass()->isFinal()) {
                    if (!$method->isPublic()) {
                        continue;
                    }
                }

                $xml = $this->xml();

                $methodXml = null;
                if ($xml->methods) {
                    if ($xml->methods->method) {
                        foreach ($xml->methods->method as $mn) {
                            $methodNameFromNode = \trim($mn['name']);
                            $methodName         = \trim($method->getName());

                            if ($methodNameFromNode === $methodName) {
                                $methodXml = $mn;
                            }
                        }
                    }
                }

                if (null === $methodXml) {
                    $methodXml = \simplexml_load_string(\sprintf('<method name="%s" />',
                                                                 \htmlspecialchars($method->getName())));
                }

                $methodList[] = new Method_($this, $method, $methodXml);
            }

            \usort($methodList, function(Method_ $x, Method_ $y) {
                return \strcasecmp($x->reflector()->getName(),
                                   $y->reflector()->getName());
            });

            $this->_methods = $methodList;
        }

        return $this->_methods;
    }

    /**
     * @return \ReflectionClass
     */
    public function reflector() {
        return parent::reflector();
    }

    public function syntax(Language $lang = null, $linePrefix = '') {
        $classType = $this->classType($lang);

        $syntax = $linePrefix . $classType . ' ' . $this->reflector()->getShortName() . " {\n";

        Enumerable::create($this->methods())
                  ->each(function(Method_ $x, IEachItemContext $ctx) use ($lang, &$syntax) {
                             if (!$ctx->isFirst()) {
                                 $syntax .= "\n";
                             }

                             $syntax .= $x->syntax($lang, '    ') . "\n";
                         });

        $syntax .= "}";

        return $syntax;
    }
}
