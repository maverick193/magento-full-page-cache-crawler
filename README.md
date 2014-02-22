Magento Full Page Cache Crawler
===============================

Full Page Cache is a mechanism available on E.E version of Magento that allows you to store outputs of a given URLs to a temporary files (caching files), when the same url is called by another visitor Magento will send the output stored in FPC files, this is a very useful functionnality, it allows you to :
- Make your website super fast
- Help reduce bandwidth usage and cpu load
- Help reduce memory comsuption and database stress.
- ...

The benefit of this functionnality is visible when the cache is fully populated and hot especially on websites with large catalog and lot of pages.

Magento includes a built-in cronjob which crawls the site and warm the FPC, this cron job is executed at 3 am, but you don't have any control over this cronjob, you cannot choose pages to crawl and if you have a large catalog (depending on your server's configuration) crawling all the website may take a long time and this may block other cron jobs scheduled right after the crawling job.

This extension allows you to control all the pages you want to warm, you can choose which pages when and how to crawl them (manually via the backend or shell or automatically via cronjob)

The extension have other functionnalities described bellow
