<?php

namespace WolframAlpha;

use WolframAlpha\Collections\AssumptionsCollection;

class ValidateQueryResult {

    var $parsedXml;

    private $attributes = [];

    var $assumptions = array();

    private $error = null;
    private $warnings = null;

    function __construct($rawXml)
    {
        $this->parsedXml = new \SimpleXMLElement($rawXml);

        foreach($this->parsedXml->attributes() as $key => $value)
        {
            $this->attributes[$key] = $value->__toString();
        }

        if(isset($this->parsedXml->warnings))
        {
            if(count($this->parsedXml->warnings->children()) > 0)
            {
                $this->warnings = array();
                foreach($this->parsedXml->warnings->children() as $key => $value)
                {
                    $this->warnings[$key] = $value->attributes()->text->__toString();
                }
            }
        }

        if(isset($this->parsedXml->error))
        {
            $this->error = array('code' => $this->parsedXml->error->code->__toString(), 'message' => $this->parsedXml->error->msg->__toString());
        }

        $this->populateAssumptions();
    }

    public function hasError()
    {
        return (($this->attributes['error'] === "true") ? true : false);
    }

    public function getError()
    {
        return $this->error;
    }

    public function hasWarnings()
    {
        return ($this->warnings != null) ? true : false;
    }

    public function getWarnings()
    {
        return $this->warnings;
    }

    private function populateAssumptions()
    {
        if(isset($this->parsedXml->assumptions))
        {
            $this->assumptions = new AssumptionsCollection($this->parsedXml->assumptions->assumption);
        }
    }

} 