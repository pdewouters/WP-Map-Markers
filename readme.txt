=== WP Map Markers ===
Contributors: pauldewouters 
Tags: google maps, store locator, maps
Requires at least: 3.3
Tested up to: 3.4
Stable tag: 0.9.3

WP Map Markers is a plugin that brings the Google Maps Store locator library to your WordPress website. It uses the v3 API.

== Description ==

Add new locations like posts with the Location custom post type. Display maps on your posts and pages with a simple shortcode.

Create an unlimited number of maps and display markers on them eith infobubbles that show the post thumbnail and other useful information.

The map interface lists locations near you and allows you to search with autocomplete and filter by features. The features are terms from a custom Feature taxonomy. Add as many filters as you like.

Choose from different marker icons.

== Installation ==

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page.

First, check the default settings on the Map Markers setting page
Create a map under the Maps section, not forgetting to provide the default map center coordinates. 
You can use the map on the settings page to get the latitude and longitude, and copy/paste them.

Add a few locations ( just like adding posts )
Insert a map on a page or post by using the wpmm_map shortcode.
Multiple maps on same page is not supported

Ex: [wpmm_map map="map-slug"]

The map slug is found on the list of maps page. 
Maps is a custom taxonomy 

== Screenshots ==
1. The map interface with a custom marker and an infobubble.

2. the search and filter panel with the list of nearby locations


==Readme== 

This Readme file was generated using <a href = 'http://sudarmuthu.com/wordpress/wp-readme'>wp-readme</a>, which generates readme files for WordPress Plugins.

==Changelog==

= 0.9.3 =
* Corrected logic error on settings map

= 0.9.2 =
* Corrected typo in function call which caused plugin to fail

= 0.9.1 =
* First Release
