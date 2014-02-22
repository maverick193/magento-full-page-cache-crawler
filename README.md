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

The extension have other functionnalities described bellow.

Requirements
============

- Install PHP Composer, see bellow
- Allow the use of symlinks in Magento Backend (System->Configuration->Developer->Template Settings ->"Allow Symlinks")
  There no security risk by allowing symlinks in Magento

Installation
============
### 1. Install PHP-Composer :
Download it into your project :
```
$ curl -sS https://getcomposer.org/installer | php
```
This will just check a few PHP settings and then download composer.phar to your working directory.

If you are not familiar with composer, please read the composer documentations on [getcomposer](http://getcomposer.org) website

### 2. Create/Update your root composer.json :
```json
{
    "name": "Your Project Name",
    "require": {
        "maverick/magento-fpc-crawler": "*",
        "magento-hackathon/magento-composer-installer": "*"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "git://github.com/maverick193/magento-full-page-cache-crawler.git"
        },
        {
            "type": "vcs",
            "url": "git://github.com/magento-hackathon/magento-composer-installer.git"
        }
    ],
    "extra":{
        "magento-root-dir": "htodcs/"
    }
}
```
- Update the "magento-root-dir" node (specify your Magento root folder "web/", "./", ...)
- The "magento-composer-installer" will install the module (via symlinks) in Magento folder structure, more information on [magento-composer-installer](https://github.com/magento-hackathon/magento-composer-installer)

### 3. Install Maverick FPC Crawler via composer :
```
php composer.phar install
```
Usage
=====

See how it works on [Crawler Wiki Pages](https://github.com/maverick193/magento-full-page-cache-crawler/wiki)

Support and Contribution
========================
If you have any issues with this extension, please open an issue on Github.

Any contributions are highly appreciated. If you want to contribute, please open [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Copyright and License
=====================
License   : [OSL - Open Software Licence 3.0](http://opensource.org/licenses/osl-3.0.php).

Copyright : (c) 2014 Mohammed NAHHAS
