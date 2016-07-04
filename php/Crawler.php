<?php

include_once('URLUtils.php');
include_once('Sitemap.php');
include_once('LinkInfo.php');
include_once('Requests.php');


class Crawler
{
    public $sitemap;
    public $duration;


    private $url;
    private $depth;
    private $domain;
    private $requests;

    public function __construct($url, $depth)
    {
        $this->sitemap = new Sitemap();
        $this->duration = 0;
        $this->url = $url;
        $this->depth = $depth;
        $this->domain = URLUtils::parseHostFromURL($url);
        $this->requests = new Requests();
    }

    public function crawl()
    {
        $time_start = microtime(true);

        $this->process([$this->url], $this->depth);

        $time_end = microtime(true);
        $this->duration = $time_end - $time_start;
    }

    public function process($urls, $depth)
    {
        if ($depth > 0) {
            $callback = function ($data, $info) {
                $this->extractPageInfo($this, $data, $info);
            };

            $this->requests->process($urls, $callback, $depth);
        }
    }

    /* @var $crawler Crawler
     * @var $page_data String
     * @var $page_info Array
     */
    private static function extractPageInfo($crawler, $page_data, $page_info)
    {
        $url = $page_info['url'];
        $depth = $page_info['depth'];

        if (URLUtils::URLHasFragment($url) || in_array($url, $crawler->sitemap->visited) || $depth === 0) {
            return;
        }

        $crawler->sitemap->visited[] = $url;

        //link information
        $is_url_internal = ($crawler->domain == URLUtils::parseHostFromURL($url));

        //if not internal do not visit
        if ($is_url_internal) {
            $dom = new DOMDocument('1.0');
            @$dom->loadHTML($page_data);
            unset($page_data);

            $section = '/';
            $url_parts = explode('/', $url);
            //if there's a section it has to have at least 5 parts (http:1)/(2)/(domain3)/(section4)/(5)
            if (count($url_parts) >= PARTS_TO_SECTION) {
                $section = $url_parts[SECTION_POSITION];
            }

            $pos = count($crawler->sitemap->internal[$section]);
            $linkInfo = new LinkInfo();
            $linkInfo->URL = $url;

            $title = $dom->getElementsByTagName('title');
            $linkInfo->title = $title->item(0)->nodeValue;

            $crawler->sitemap->internal[$section][$pos] = $linkInfo;

            $anchors = $dom->getElementsByTagName('a');
            $dom = NULL;

            $urls = array();

            /* @var $anchors DOMNodeList
             * @var $element DOMNode
             */
            foreach ($anchors as $element) {
                $href = $element->getAttribute('href');

                //if the href is relative or it's an edge case...
                if (URLUtils::URLStartsWith($href, 'http') == false) {
                    //this ones we ignore
                    if (URLUtils::URLStartsWith($href, 'mailto') || URLUtils::URLStartsWith($href, 'javascript:')) {
                        $crawler->sitemap->visited[] = $href;
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

                //reducing the number of calls
                if (in_array($href, $crawler->sitemap->visited) == false && in_array($href, $urls) == false) {
                    if ($crawler->domain != URLUtils::parseHostFromURL($href)) {
                        $crawler->sitemap->external[] = $href;
                        $crawler->sitemap->visited[] = $href;
                    } else {
                        $urls[] = $href;
                    }
                }
            }

            $crawler->process($urls, $depth - 1);
        } else {
            $crawler->sitemap->external[] = $url;
        }
    }
}