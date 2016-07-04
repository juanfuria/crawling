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

Now to the juicy part. This was a dirt'n'quick approach and uses a very basic recursive approach to solve the crawling. 
That's sort of OK if the site is quite small or if you can control the max execution time of the PHP script and you're not in a hurry.
It obviously sucks and it's not a good solution. Next step is implementing concurrent asynchronous calls and therefore eliminating the download-one-and-process-one way.

It's not very nice the lack of feedback while the website is being crawled and, as said, works best with relatively small webs. A good one to try is `http://tobiasahlin.com/spinkit/`