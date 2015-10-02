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
use \phpDocumentor\Reflection\DocBlock\Tag\ParamTag;
use \phpDocumentor\Reflection\DocBlock\Tag\ReturnTag;
use \phpLINQ\Docs\Language;
use \phpLINQ\Docs\SyntaxHandler;
use \phpLINQ\Docs\Type_;
use \System\Collections\IEachItemContext;
use \System\Collections\IIndexedItemContext;
use \System\Linq\Enumerable;


class Method_ extends Reflectable {
    use SyntaxHandler;


    /**
     * @var Class_
     */
    private $_class;


    public function __construct(Class_ $cls, \ReflectionMethod $method, \SimpleXMLElement $xml) {
        $this->_class = $cls;

        parent::__construct($cls->project(), $method, $xml);
    }


    /**
     * @return Class_
     */
    public function class_() {
        return $this->_class;
    }

    protected function findSummary(
        \ReflectionClass $scopeClass,
        \ReflectionMethod $scopeMethod,
        Language $lang = null,
        &$alreadyScopedClasses = array()
    ) {
        foreach ($alreadyScopedClasses as $sc) {
            /* @var \ReflectionClass $sc */;

            if ($sc->getName() === $scopeClass->getName()) {
                return null;
            }
        }

        $alreadyScopedClasses[] = $scopeClass;

        $doc = new DocBlock($scopeMethod);

        $result = $doc->getShortDescription();

        if ('{@inheritdoc}' === \trim(\strtolower($result))) {
            $result = null;

            $typesToCheck = \array_merge($scopeClass->getTraits(),
                                         [$scopeClass->getParentClass()],
                                         $scopeClass->getInterfaces());

            foreach ($typesToCheck as $type) {
                if (!$type instanceof \ReflectionClass) {
                    continue;
                }

                $matchingMethod = null;
                foreach ($type->getMethods() as $otherMethod) {
                    if ($otherMethod->getName() !== $scopeMethod->getName()) {
                        continue;
                    }

                    $matchingMethod = $otherMethod;
                }

                if (!$matchingMethod instanceof \ReflectionMethod) {
                    continue;
                }

                $summary = \trim($this->findSummary($type, $matchingMethod, $lang, $alreadyScopedClasses));
                if ('' !== $summary) {
                    $result = $summary;
                }
            }
        }

        $result = \trim($result);

        return '' !== $result ? $result : null;
    }

    public function generateDocumentation(Language $lang = null) {
        $this->generateIndexFile($lang);
    }

    protected function generateIndexFile(Language $lang = null) {
        $file = $this->createNewFile($this->getDocumentFilename($lang));
        if (!\is_resource($file)) {
            return false;
        }

        $header = $this->header();
        $footer = $this->footer();

        $me = $this;

        $this->writeTo($file, function() use ($footer, $header, $lang, $me) {
            $doc = $me->docBlock();

            $declaredClass = $me->class_();
            $classLink = \trim($me->findLinkForClass($declaredClass->reflector(), $lang));

            echo $header;

            echo '<div class="row">';

            echo '<ul class="breadcrumbs">';
            echo '<li>';
            echo '<a href="' . \htmlspecialchars($this->project()->getDocumentFilename($lang)) . '">Home</a>';
            echo '</li>';
            echo '<li class="unavailable">';
            echo '<a href="#">';
            echo \htmlentities($me->parseNamespace($declaredClass->reflector()->getNamespaceName()));
            echo '</a>';
            echo '</li>';
            echo '<li>';

            if ('' !== $classLink) {
                echo '<a href="' . \htmlspecialchars($classLink) . '">';
            }

            echo \htmlentities($declaredClass->reflector()->getShortName());

            if ('' !== $classLink) {
                echo '</a>';
            }

            echo '</li>';
            echo '<li class="current">';
            echo '<a href="#">';
            echo \htmlentities($me->reflector()->getName());
            echo '</a>';
            echo '</li>';
            echo '</ul>';

            echo '<h1>';
            echo \htmlentities($me->reflector()->getName()) . '() method';
            echo '</h1>';

            echo '</div>';

            $syntax = $this->syntax($lang);

            /* @var ReturnTag $returnTag */
            $returnTag = Enumerable::create($doc->getTagsByName('return'))
                                   ->lastOrDefault();

            echo '<pre style="background-color: transparent;">';
            echo '<code class="php">';
            echo $me->parseForHtmlOutput($syntax);
            echo '</code>';
            echo '</pre>';

            if ($returnTag instanceof ReturnTag) {
                /* @var Type_[] $types */
                $types = Type_::fromReturnTag($returnTag);
                if (empty($types)) {
                    $types = [Type_::fromName('mixed')];
                }

                echo '<!-- ' . var_export($types, true) . ' -->';

                echo '<h2>Returns</h2>';

                echo '<table style="width: 100%;">';
                echo '<tbody>';

                $typeCount = \count($types);

                $getTypeName = function(Type_ $type) {
                    $result = $type->name();

                    if (null !== $type->reflector()) {
                        $result = $type->reflector()->getShortName();
                    }

                    return $result;
                };

                Enumerable::create($types)
                          ->each(function(Type_ $x, IEachItemContext $ctx) use ($declaredClass, $getTypeName, $lang, $me, $returnTag, $typeCount) {
                                     $typeName = $getTypeName($x);

                                     if (0 === \stripos($typeName, "\\")) {
                                         if (!interface_exists($typeName) &&
                                             !trait_exists($typeName) &&
                                             !class_exists($typeName)) {

                                             $newType = $declaredClass->reflector()->getNamespaceName() . $typeName;

                                             if (interface_exists($newType) ||
                                                 trait_exists($newType) ||
                                                 class_exists($newType)) {

                                                 $x = Type_::fromClass(new \ReflectionClass($newType),
                                                                       $lang,
                                                                       $me->project());

                                                 $typeName = $getTypeName($x);
                                             }
                                         }
                                     }

                                     echo '<td>';

                                     $typeLink = \trim($x->link());

                                     if ('' !== $typeLink) {
                                         echo '<a href="'. \htmlspecialchars($typeLink) . '">';
                                     }

                                     echo \htmlentities($typeName);

                                     if ('' !== $typeLink) {
                                         echo '</a>';
                                     }

                                     echo '</td>';

                                     if ($ctx->isFirst()) {
                                         echo '<td rowspan="' . \trim($typeCount) . '">';

                                         echo $me->parseForHtmlOutput($returnTag->getDescription());

                                         echo '</td>';
                                     }
                                 });

                echo '</tbody>';
                echo '</table>';
            }

            echo <<<'EOT'
<script type="text/javascript">

    jQuery(document).ready(function() {
        hljs.initHighlighting();
    });

</script>
EOT;


            echo $footer;
        });
    }

    public function getDocumentFilename(Language $lang = null) {
        $className = $this->class_()->reflector()->getName();
        $className = \str_ireplace("\\", '.', $className);

        $methodName = $this->reflector()->getName();

        return \sprintf('method.%s.%s.%s.html',
                        $lang->id(), $className, $methodName);
    }

    /**
     * @return \ReflectionMethod
     */
    public function reflector() {
        return parent::reflector();
    }

    public function summary(Language $lang = null) {
        $result = parent::summary($lang);

        if ('{@inheritdoc}' === \trim(\strtolower($result))) {
            $result = $this->findSummary($this->class_()->reflector(),
                                         $this->reflector(),
                                         $lang);
        }

        return $result;
    }

    public function syntax(Language $lang = null, $linePrefix = '') {
        $doc = $this->docBlock();

        $visibility = null;
        if (!$this->reflector()->getDeclaringClass()->isInterface()) {
            if ($this->reflector()->isPublic()) {
                $visibility = 'public ';
            }
            else if ($this->reflector()->isProtected()) {
                $visibility = 'protected ';
            }
            else if ($this->reflector()->isPrivate()) {
                $visibility = 'private ';
            }
        }

        $isStatic = null;
        if ($this->reflector()->isStatic()) {
            $isStatic = 'static ';
        }

        $isAbstract = null;
        if (!$this->reflector()->getDeclaringClass()->isInterface()) {
            if ($this->reflector()->isAbstract()) {
                $isAbstract = 'abstract ';
            }
        }

        $isFinal = null;
        if ($this->reflector()->isFinal()) {
            $isFinal = 'final ';
        }

        $syntax = \sprintf('%s%s%s%s%sfunction %s(',
                           $linePrefix,
                           $isAbstract,
                           $visibility,
                           $isStatic,
                           $isFinal,
                           $this->reflector()->getName());

        /* @var ParamTag[] $paramTags */
        $paramTags = $doc->getTagsByName('param');

        $findParamTag = function(\ReflectionParameter $rp) use ($paramTags) {
            $paramName = '$' . $rp->getName();

            $result = null;

            foreach ($paramTags as $pt) {
                if (\trim($pt->getVariableName()) === $paramName) {
                    $result = $pt;
                }
            }

            return $result;
        };

        $parameters = Enumerable::create($this->reflector()->getParameters())
                                ->select(function(\ReflectionParameter $x, IIndexedItemContext $ctx) use ($findParamTag, $linePrefix, $syntax) {
                                             $result = !$ctx->isFirst() ? "\n" . \str_repeat(' ', \strlen($syntax))
                                                                        : '';

                                             $tag = $findParamTag($x);
                                             if ($tag instanceof ParamTag) {
                                                 $types = Enumerable::create($tag->getTypes())
                                                                    ->select('$x => \trim($x)')
                                                                    ->distinct('($x, $y) => \strtolower($x) === \strtolower($y)')
                                                                    ->toArray();

                                                 if (!empty($types)) {
                                                     $result .= \implode(' | ', $types) . ' ';
                                                 }
                                             }

                                             $result .= '$' . $x->getName();

                                             if ($x->isOptional()) {
                                                 $result .= ' = ' . \var_export($x->getDefaultValue(), true);
                                             }

                                             return $result;
                                         })
                                ->joinToString(',');

        $syntax .= \sprintf('%s)',
                            $parameters);

        $syntax .= ';';

        return $syntax;
    }
}
