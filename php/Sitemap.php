<?php

class Sitemap
{
    public $visited;
    public $internal;
    public $external;

    public function __construct()
    {
        $this->visited = array();
        $this->internal = array();
        $this->external = array();
    }
}