=== WP Map Markers ===
Contributors: pauldewouters 
Tags: google maps, store locator, maps
Requires at least: 3.3
Tested up to: 3.4
Stable tag: 0.9.9.1

WP Map Markers is a plugin that brings the Google Maps Store locator library to your WordPress website. It uses the v3 API.

== Description ==

Add new locations like posts with the Location custom post type. Display maps on your posts and pages with a simple shortcode.

Create an unlimited number of maps and display markers on them with infobubbles that show the post thumbnail and other useful information.

The map interface lists locations near you and allows you to search with autocomplete and filter by features. The features are terms from a custom Feature taxonomy. Add as many filters as you like.

Choose from different marker icons.

**Follow this plugin on [Git Hub](http://pdewouters.github.com/WP-Map-Markers/)**

== Installation ==

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page.

First, check the default settings on the Map Markers setting page
Create a map under the Maps section, not forgetting to provide the default map center coordinates. 
You can use the map on the settings page to get the latitude and longitude, and copy/paste them.

Add a few locations ( just like adding posts )
Insert a map on a page or post by using the wpmm_map shortcode.
Multiple maps on same page is not supported

Insert a map easily by selecting it from a dropdown menu located in the post editor

== Shortcode ==
Ex: [wpmm_map map="1"] where "1" is the ID of the Map found in the Map category for the locations.

The map slug is found on the list of maps page. 
Maps is a custom taxonomy 

More information at [WP Map Markers.com](http://wpmapmarkers.com/).

[youtube http://www.youtube.com/watch?v=5zA8G73oZ0k]

== Screenshots ==

1. Screenshot 1
2. Screenshot 2
3. Screenshot 3
4. Screenshot 4

==Changelog==

= 0.9.9.1 =

settings bugs fixed

= 0.9.9 =
introduce plugin settings version check and upgrade or install
previous version plugin settings will be overwritten with new settings, so please reset your defaults after upgrading

= 0.9.8 =
removed some debugging code, cleanup

= 0.9.7 =
added map selector in post editor screen.
map shortcode now uses ID instead of slug. Please update shortcodes to (example ID) [wpmm_map map="1"] 

= 0.9.6 =
corrected localization bugs following review

= 0.9.5 =
added youtube video and screenshots

= 0.9.4 =
* forgot to update plugin stable tag on previous commit, so stuck on previous tag (svn noob)

= 0.9.3 =
* Corrected logic error on settings map

= 0.9.2 =
* Corrected typo in function call which caused plugin to fail

= 0.9.1 =
* First Release
