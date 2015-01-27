<?php

namespace System\Collections\Generic;

final class Grouping implements IGrouping {
    private $_items;
    private $_key;
    
    
    public function __construct($key, IEnumerable $items) {
        $this->_key = $key;
        $this->_items = $items;
    }
    
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IGrouping::getIterator()
     */
    public function getIterator() {
        return $this->_items;
    }
    
    /**
     * (non-PHPdoc)
     * @see \System\Collections\Generic\IGrouping::key()
     */
    public function key() {
        return $this->_key;
    }
}
