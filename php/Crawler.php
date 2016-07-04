<?php

include_once('URLUtils.php');
include_once('Sitemap.php');
include_once('LinkInfo.php');


class Crawler
{
    public $sitemap;
    public $duration;


    private $url;
    private $depth;
    private $domain;

    public function __construct($url, $depth)
    {
        $this->url = $url;
        $this->depth = $url;
        $this->domain = URLUtils::parseHostFromURL($url);
        $this->duration = 0;
    }

    public function crawl()
    {
        $time_start = microtime(true);

        $this->sitemap = $this->recursiveCrawl($this->url, $this->depth, new Sitemap(), $this->domain);

        $time_end = microtime(true);
        $this->duration = $time_end - $time_start;
    }

    /* @var $url String
     * @var $depth Integer
     * @var $sitemap Sitemap
     * @var $base_domain String
     * @return Sitemap
     */
    private static function recursiveCrawl($url, $depth, $sitemap, $base_domain)
    {
        if (URLUtils::URLHasFragment($url) || in_array($url, $sitemap->visited) || $depth === 0) {
            return $sitemap;
        }

        $sitemap->visited[] = $url;

        //link information
        $is_url_internal = ($base_domain == URLUtils::parseHostFromURL($url));

        //if not internal do not visit
        if ($is_url_internal) {
            $dom = new DOMDocument('1.0');
            @$dom->loadHTMLFile($url);


            $section = '/';
            $url_parts = explode('/', $url);
            //if there's a section it has to have at least 5 parts (http:1)/(2)/(domain3)/(section4)/(5)
            if (count($url_parts) >= PARTS_TO_SECTION) {
                $section = $url_parts[SECTION_POSITION];
            }

            $pos = count($sitemap->internal[$section]);
            $linkInfo = new LinkInfo();
            $linkInfo->URL = $url;

            $title = $dom->getElementsByTagName('title');
            $linkInfo->title = $title->item(0)->nodeValue;

            $sitemap->internal[$section][$pos] = $linkInfo;

            $anchors = $dom->getElementsByTagName('a');
            $dom = NULL;

            /* @var $anchors DOMNodeList
             * @var $element DOMNode
             */
            foreach ($anchors as $element) {
                $href = $element->getAttribute('href');

                //if the href is relative or it's an edge case...
                if (URLUtils::URLStartsWith($href, 'http') == false) {
                    //this ones we ignore
                    if (URLUtils::URLStartsWith($href, 'mailto') || URLUtils::URLStartsWith($href, 'javascript:')) {
                        break;
                    } //if you just used // in the href we need to resolve the protocol
                    else if (URLUtils::URLStartsWith($href, '//')) {
                        $parts = parse_url($url);
                        $href = $parts['scheme'] . ':' . $href;
                    } //relative urls that we need to rebuild
                    else {
                        $path = '/' . ltrim($href, '/');
                        if (extension_loaded('http')) {
                            $href = http_build_url($url, array('path' => $path));
                        } else {
                            $parts = parse_url($url);
                            $href = $parts['scheme'] . '://';
                            if (isset($parts['user']) && isset($parts['pass'])) {
                                $href .= $parts['user'] . ':' . $parts['pass'] . '@';
                            }
                            $href .= $parts['host'];
                            if (isset($parts['port'])) {
                                $href .= ':' . $parts['port'];
                            }
                            $href .= $path;
                        }
                    }
                }
                $sitemap = Crawler::recursiveCrawl($href, $depth - 1, $sitemap, $base_domain);
            }
        } else {
            $sitemap->external[] = $url;
        }

        return $sitemap;
    }
}