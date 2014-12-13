<?php

class AssumptionValueTest extends PHPUnit_Framework_TestCase {

    var $assumptionValue;

    function prepare()
    {
        $parsedAssumptionXml = new SimpleXMLElement(file_get_contents(dirname(__FILE__)."/xml/assumptions_fragment.xml"));
        $this->assumptionValue = new \WolframAlpha\AssumptionValue($parsedAssumptionXml->assumption[0]->value[0]);
    }

    function testAssumptionValueCreation()
    {
        $this->prepare();
        $this->assertInstanceOf('WolframAlpha\\AssumptionValue', $this->assumptionValue);
        $this->assertEquals('Character', $this->assumptionValue->name);
        $this->assertEquals('a character', $this->assumptionValue->desc);
        $this->assertEquals('*C.e-_*Character-', $this->assumptionValue->input);
    }

} 