<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kloubert
 * Date: 30.09.2015
 * Time: 22:03
 */

namespace phpLINQ\Docs\Classes;


class Method_ extends Reflectable {
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

    public function generateDocumentation() {
        //TODO
    }

    /**
     * @return \ReflectionMethod
     */
    public function reflector() {
        return parent::reflector();
    }
}
