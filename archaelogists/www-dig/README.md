This is a fun php app to be run from your laptop or some website to
find pages with images, and then you will be able to quickly fill out 
information about the images, and then submit them to the 
openclipart library. This is meant to be easy and fun.


HOWTO
=====

* Run this script by linking to this www-dig tool as a public folder for
  your webserver, apache or nginx.


TODO
====

* if find a link to file with wiki in the hostname, then test the file
  link to see if the file is real, or if is another html page with options
  for the real file. This is to fix how mediawiki handles uploaded
  attachments.
* make the left-pane collapsible so only have scraped view
* make easier to use on tablet
* remove some options for testing
* add ability to pull from list of urls of known public domain images
* add ability to flag the pulled source of URLs that a url has been pulled


FEATURES
========

* Add default template
* make inferences about the title, description and tags from the page
* upon load, pull up a new url that has public domain content in it
* automatically load the url of the file and the date into the
  description field of the first crawl


WISHES
======

* allow for surfing in the left-hand pane, and then automatic update of
  the right hand side scrape of images
* allow for linking the image on the right hand side with the location
  on the left hand side
