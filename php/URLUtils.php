<?php

class URLUtils
{
    public static function parseHostFromURL($url)
    {
        return parse_url($url, PHP_URL_HOST);
    }

    public static function validateURL($url)
    {
        return (!filter_var($url, FILTER_VALIDATE_URL) === false);
    }

    public static function URLStartsWith($url, $string)
    {
        return $string === '' || strrpos($url, $string, -strlen($url)) !== false;
    }

    public static function URLEndsWith($url, $string)
    {
        return $string === '' || (($temp = strlen($url) - strlen($string)) >= 0 && strpos($url, $string, $temp) !== false);
    }

    public static function URLHasFragment($url)
    {
        return URLUtils::URLEndsWith($url, '#') || is_null(parse_url($url, PHP_URL_FRAGMENT)) == false;
    }
}