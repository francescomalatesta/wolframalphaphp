<?php

namespace WolframAlpha;

class Image {

    private $src;
    private $alt;
    private $title;
    private $width;
    private $height;

    function __construct($src, $alt, $title, $width, $height)
    {
        $this->src = $src;
        $this->alt = $alt;
        $this->title = $title;
        $this->width = $width;
        $this->height = $height;
    }

    public function __get($name)
    {
        return isset($this->{$name}) ? $this->{$name} : null;
    }

}