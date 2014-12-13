<?php

namespace WolframAlpha\Collections;

use WolframAlpha\Pod;

class PodsCollection implements \ArrayAccess, \Countable {

    var $parsedXml;

    private $pods = array();

    function __construct($parsedPodsXml)
    {
        $this->parsedXml = $parsedPodsXml;

        foreach($parsedPodsXml as $pod)
        {
            $this->pods[(string)$pod->attributes()['id']] = new Pod($pod);
        }
    }

    public function has($podId)
    {
        return isset($this->pods[$podId]) ? true : false;
    }

    public function find($podId)
    {
        return $this->has($podId) ? $this->pods[$podId] : null;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->pods[] = $value;
        } else {
            $this->pods[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->pods[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->pods[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->pods[$offset]) ? $this->pods[$offset] : null;
    }

    public function count()
    {
        return count($this->pods);
    }
}