<?php

namespace WolframAlpha;

class Subpod {

    var $parsedXml;

    private $elements = array();

    function __construct($subpodParsedXml)
    {
        $this->parsedXml = $subpodParsedXml;

        foreach($subpodParsedXml->children() as $key => $element)
        {
            $value = '';

            switch($key)
            {
                case 'img':
                    $value = new Image(
                        (string)$element->attributes()['src'],
                        (string)$element->attributes()['alt'],
                        (string)$element->attributes()['title'],
                        (int)$element->attributes()['width'],
                        (int)$element->attributes()['height']
                    );
                    break;

                default:
                    $value = $element->__toString();
                    break;
            }

            $this->elements[$key] = $value;
        }
    }

    function __get($name)
    {
        return isset($this->elements[$name]) ? $this->elements[$name] : null;
    }

} 