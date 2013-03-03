---
title: SMF SEF urls
author: bluegray
layout: post
permalink: /2007/internet/smf-sef-urls/61/
categories:
  - internet
  - programming
---
# 

Testing out the latest version of the SMF forum. The standard installation have no problems with crawlers. You do need incoming links from somewhere though. GoogleBot was crawling less than a day after installation, although there were already some pages from that domain that were in the google index. I didn't submit a sitemap or do anything special other than getting inbound links.

The 'SEF links' option in SMF looks like it is useless now and only needed if you want some ancient stupid bots that don't like dynamic content. But those are probably not worth worrying about.

Also been trying [the pretty urls mod for SMF][1]. While it won't make a difference to the bots, the keywords in the urls might make a difference. Only downside I see is the duplicate content from earlier indexed urls, but 301 redirects should fix that. Because the standard urls look more dynamic, google might prefer the pretty urls more. Don't know if urls might get too long with this mod, will have to check.

 [1]: http://www.simplemachines.org/community/index.php?topic=146969.0

Will try an unknown domain with a clean install next.