<?php

class QueryResultTest extends PHPUnit_Framework_TestCase {

    var $result;

    function testQueryResultCreation()
    {
        $this->prepare('normal');

        $this->assertInstanceOf('WolframAlpha\\QueryResult', $this->result);
    }

    function testSuccessfulQueryResult()
    {
        $this->prepare('normal');

        $this->assertEquals(false, $this->result->hasProblems());
        $this->assertEquals(false, $this->result->hasError());
    }

    function testQueryResultWithProblems()
    {
        $this->prepare('problems');

        $this->assertEquals(true, $this->result->hasProblems());
    }

    function testQueryResultWithErrors()
    {
        $this->prepare('errors');

        $this->assertEquals(true, $this->result->hasError());
        $this->assertInternalType('array', $this->result->getError());
    }

    function testQueryResultWithWarnings()
    {
        $this->prepare('warnings');

        $this->assertEquals(true, $this->result->hasWarnings());
        $this->assertInternalType('array', $this->result->getWarnings());
    }

    function testQueryResultWithAssumptions()
    {
        $this->prepare('assumptions');

        $this->assertEquals(1, count($this->result->assumptions));
        $this->assertEquals('Chemical', $this->result->assumptions['Clash']->values['Chemical']->name);
    }

    function testQueryResultWithPods()
    {
        $this->prepare('multiple_pods');

        $this->assertInstanceOf('WolframAlpha\\Collections\\PodsCollection', $this->result->pods);
        $this->assertEquals(2, count($this->result->pods));
        $this->assertEquals('Input interpretation', $this->result->pods['Input']->title);
    }

    function testQueryResultWithTips()
    {
        $this->prepare('warnings');

        $this->assertEquals(1, count($this->result->getTips()));
        $this->assertEquals('Check your spelling, and use English', $this->result->getTips()[0]);
    }

    function testQueryResultWithSuggestions()
    {
        $this->prepare('warnings');

        $this->assertEquals(1, count($this->result->getSuggestions()));
        $this->assertEquals('frances split', $this->result->getSuggestions()[0]);
    }

    function prepare($type)
    {
        $this->result = null;

        switch($type){
            case 'normal':
                $xml = file_get_contents(dirname(__FILE__)."/xml/normal.xml");
                break;

            case 'problems':
                $xml = file_get_contents(dirname(__FILE__)."/xml/problems.xml");
                break;

            case 'errors':
                $xml = file_get_contents(dirname(__FILE__)."/xml/errors.xml");
                break;

            case 'warnings':
                $xml = file_get_contents(dirname(__FILE__)."/xml/warnings.xml");
                break;

            case 'assumptions':
                $xml = file_get_contents(dirname(__FILE__)."/xml/assumptions.xml");
                break;

            case 'multiple_pods':
                $xml = file_get_contents(dirname(__FILE__)."/xml/multiple_pods.xml");
                break;
        }

        $this->result = new \WolframAlpha\QueryResult($xml);
    }

} 