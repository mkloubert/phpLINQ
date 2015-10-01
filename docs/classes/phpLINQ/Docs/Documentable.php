<?php
/**
 * Created by PhpStorm.
 * User: Marcel Kloubert
 * Date: 30.09.2015
 * Time: 21:41
 */

namespace phpLINQ\Docs;


abstract class Documentable {
    /**
     * @var Project
     */
    private $_project;
    /**
     * @var \SimpleXMLElement
     */
    private $_xml;


    protected function __construct(Project $proj, \SimpleXMLElement $xml) {
        $this->_project = $proj;
        $this->_xml = $xml;
    }


    abstract public function generateDocumentation();

    /**
     * @return Project
     */
    public function project() {
        return $this->_project;
    }

    /**
     * @return \SimpleXMLElement
     */
    public function xml() {
        return $this->_xml;
    }
}
