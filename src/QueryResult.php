<?php

namespace WolframAlpha;

use WolframAlpha\Collections\AssumptionsCollection;
use WolframAlpha\Collections\PodsCollection;

class QueryResult {

    var $parsedXml;

    var $attributes = [];

    var $pods = null;
    var $assumptions = null;

    var $error = null;
    var $warnings = null;

    var $tips = array();
    var $suggestions = array();

    function __construct($rawXml)
    {
        $this->parsedXml = new \SimpleXMLElement($rawXml);

        // populating attributes array
        foreach($this->parsedXml->attributes() as $key => $value)
        {
            $this->attributes[$key] = $value->__toString();
        }

        // populating warnings data
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

        // populating error data
        if(isset($this->parsedXml->error))
        {
            $this->error = array('code' => $this->parsedXml->error->code->__toString(), 'message' => $this->parsedXml->error->msg->__toString());
        }

        // populating tips and suggestions data
        if(isset($this->parsedXml->tips))
        {
            foreach($this->parsedXml->tips->tip as $tip)
            {
                $this->tips[] = (string)$tip->attributes()['text'];
            }
        }

        if(isset($this->parsedXml->didyoumeans))
        {
            foreach($this->parsedXml->didyoumeans->didyoumean as $suggestion)
            {
                $this->suggestions[] = (string)$suggestion;
            }
        }

        $this->populateCollections();
    }

    public function __get($name)
    {
        return (isset($this->attributes[$name]) ? $this->attributes[$name] : null);
    }

    public function hasProblems()
    {
        return (($this->attributes['success'] === "false") ? true : false);
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

    public function getTips()
    {
        return $this->tips;
    }

    public function getSuggestions()
    {
        return $this->suggestions;
    }

    private function populateCollections()
    {
        $this->populateAssumptions();
        $this->populatePods();
    }

    private function populateAssumptions()
    {
        if(isset($this->parsedXml->assumptions))
        {
            $this->assumptions = new AssumptionsCollection($this->parsedXml->assumptions->assumption);
        }
    }

    private function populatePods()
    {
        if(isset($this->parsedXml->pod))
        {
            $this->pods = new PodsCollection($this->parsedXml->pod);
        }
    }

}