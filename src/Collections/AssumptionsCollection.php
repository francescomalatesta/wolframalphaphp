<?php

namespace WolframAlpha\Collections;

use WolframAlpha\Assumption;

class AssumptionsCollection implements \ArrayAccess, \Countable {

    var $parsedXml;

    private $assumptions;

    function __construct($parsedAssumptionsXml)
    {
        $this->parsedXml = $parsedAssumptionsXml;

        foreach($parsedAssumptionsXml as $assumption)
        {
            $this->assumptions[(string)$assumption->attributes()['type']] = new Assumption($assumption);
        }
    }

    public function has($assumptionType)
    {
        return isset($this->assumptions[$assumptionType]) ? true : false;
    }

    public function find($assumptionType)
    {
        return $this->has($assumptionType) ? $this->assumptions[$assumptionType] : null;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->assumptions[] = $value;
        } else {
            $this->assumptions[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->assumptions[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->assumptions[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->assumptions[$offset]) ? $this->assumptions[$offset] : null;
    }

    public function count()
    {
        return count($this->assumptions);
    }
}