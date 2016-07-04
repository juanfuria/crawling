![Repo logo](nightcrawler.jpg)

# Crawling
A quick prototype of a webcrawler sitemap generator

The idea was to write a simple web crawler. I choose PHP as I feel very confident with the language. 
It's great for scripting and it's super simple to run.
However (and we'll get to it) it turned out to be relatively terrible for a webcrawler.

# Use
The crawler accepts a URL, e.g.: http://example.com and follows all links inside the same domain.
It outputs a list divided between internal and external links, it also attempts to classify internal links in different sections.
 
# Deploy 

Download the repo [from this link](https://github.com/juanfuria/crawling/archive/master.zip). Unzip and copy to the appropriate directory.
The simplest way to deploy it is if you already have PHP installed in your computer:

`/usr/bin/php -S localhost:8080 -t "/path_to_crawling"`

Alternatively you can deploy it to a web server that supports PHP. You can see a live demo [here](http://juan.is/crawling/)
 
# Trade-offs 

After the first serial recursive attempt [See tag 1.0]() I felt it really needed asynchronous concurrent processing.
Works generally well, but I still get problems with large sites if I turn the depth to more than 3. 

With some sites the requests are just too many and too quick, so I'm getting rate limited. I made an attempt to add a rolling window limit but it's tricky to tweak. Needs improvements.