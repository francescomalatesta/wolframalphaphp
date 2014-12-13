<?php

class PodTest extends PHPUnit_Framework_TestCase {

    var $pod;

    function prepare()
    {
        $parsedXml = new SimpleXMLElement(file_get_contents(dirname(__FILE__)."/xml/normal.xml"));
        $parsedPodXml = $parsedXml->xpath('pod')[0];

        $this->pod = new \WolframAlpha\Pod($parsedPodXml);
    }

    function testPodCreation()
    {
        $this->prepare();

        $this->assertInstanceOf('WolframAlpha\\Pod', $this->pod);
    }

    function testPodAttributes()
    {
        $this->prepare();

        $this->assertEquals('Input interpretation', $this->pod->title);
    }

    function testPodSubpodsCount()
    {
        $this->prepare();

        $this->assertEquals(1, count($this->pod->subpods));
    }

}