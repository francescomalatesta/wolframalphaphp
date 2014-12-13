<?php

class EngineTest extends PHPUnit_Framework_TestCase {

    function testRequestExecution()
    {
        $engine = new WolframAlpha\Engine('1234567890');
        $result = $engine->process('test');

        $this->assertInstanceOf('WolframAlpha\\QueryResult', $result);
    }

}