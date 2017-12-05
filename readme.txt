=== Easy Facebook Feed ===
Contributors: timwass
Author: Tim
Tags: facebook, feed, widget, plugin, page, shortcode
Requires at least: 3.0.1
Tested up to: 4.9
Stable tag: 3.0.15
Version: 3.0.15
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Get your Facebook posts on your Wordpress website in an easy way!

== Description ==

[Demo](http://easy-facebook-feed.nl/)

Get your Facebook posts on your Wordpress website in an easy way. Features in Easy Facebook Feed include:

*   Displays shared links, video's, status updates, events and photo's from your Facebook page.
*   Multiple feeds.
*   Responsive layout.
*   Uses the colors of your theme.
*   Adjustable number of posts.
*   Usable as full page (shortcode).
*   Usable as widget.
*	Caching for optimal performance
*   Translation ready
*   Easy to use.

== Installation ==

= Installation =
1. Upload `easy-facebook-feed` to the `/wp-content/plugins/` directory,
2. Activate the plugin through the 'Plugins' menu in WordPress,
3. Go to the Easy Facebook settings and add your own Facebook ID,

= Display as page =
4. Place `[easy_facebook_feed]` on your page,
5. Optional: if you want to use different feeds on different pages you can add parameters to the shortcode,
for example: `[easy_facebook_feed id=bbcnews limit=3]`.

= Display as widget =
4. Go to Appearance -> Widgets,
5. Add the Easy Facebook Feed widget to your widget area

And thats it, you are done!

== Frequently Asked Questions ==

= Where can I find my facebook id? =

Your facebook id can be found in the url of your facebook page, for example: https://www.facebook.com/bbcnews, is this example 'bbcnews' is the facebook id.

= Why does the shortcode generate no content? =

* Check if your Facebook ID is correctly typed.
* Make sure your Facebook page has no age restrictions.
* Make sure your server supports php-curl.
* Make sure allow_url_fopen is enabled in your php.ini.

= Can I use custom shortcodes? =

There are 2 optional shortcode parameters, id and limit. Id overwrites your default facebook id. Limit overwrites your default post limit. This way you can use different shortcodes for different pages and situations. Example: `[easy_facebook_feed id=bbcnews limit=5]`.

= Can I use multiple feeds on a single page? =

You can add multiple Facebook feeds to the shortcode id separated by comma. For example: `[easy_facebook_feed id=bbcnews,natgeo]`.

= Can I use Easy Facebook Feed in my templates? =

Yes you can, simple add `<?php echo do_shortcode('[easy_facebook_feed]'); ?>` to your template.

= Is Easy Facebook Feed using caching? =

Yes, to offer optimal website performance Easy Facebook Feed will automatically cache your feed data.

= Why do some images not display? =

This is an issue with Jetpack Photon, Photon is breaking some of the image urls. Its recommended to disable Photon.

== Screenshots ==

1. full-page Easy Facebook Feed.
2. Easy Facebook Feed as widget in a sidebar.

== Changelog ==

= 3.0.11 =
* Minor css fixes

= 3.0.9 =
* Added extra hooks
* CSS fixes

= 3.0.8 =
* url bugfix

= 3.0.4 =
* CSS bugfix
* Added demo url

= 3.0.3 =
* Caching default value bugfix

= 3.0.2 =
* Added Danish translations (credits to Mathias)
* Admin bugfixes

= 3.0.0 =
* Code cleanup
* Bugfixes
* New caching options
* Added translation support
* Added Dutch translations

= 2.7 =
* Removed Font Awesome to avoid conflics with themes
* Fixed a curl ssl bug
* Improved caching

= 2.6 =
* Bug fixes

= 2.5 =
* Error fix

= 2.4 =
* Added caching (Credits to Alex)
* Its now possible to add multiple id's to the shortcode (Credits to Alex)
* Fixed some warning messages

= 2.3 =
* Fixed some strict php errors

= 2.2 =
* Message line-break fix

= 2.1 =
* Time ago bugfix

= 2.0 =
* Updated Facebook graph api to v2.6

= 1.9 =
* Some technical improvements
* Added curl support
* Added improved error messages
* Added scss file

= 1.8 =
* Long url overflow fix
* Image resize fix

= 1.7 =
* View link now opens in new screen

= 1.6 =
* Fixed a bug with hashtag urls

= 1.5 =
* Added support for Facebook events
* updated FAQ

= 1.4 =
* Fixed some strict php errors

= 1.3 =
* Changed alt text for Facebook image
* Css header fix

= 1.2 =
* Removed bootstrap css to avoid conflicts with custom themes
* Made shared link pictures clickable
* Url link bugfix

= 1.1 =
* Settings link bugfix

= 1.0 =
* Improved performance
* Code cleanup
* Added Hashtag support
* Added url support
* Some small bugfixes

= 0.7 =
* Added support for multiple Facebook feeds

= 0.6 =
* Facebook graph api update

= 0.5 =
* Added widget support
* Added settings shortcut on plugin page.

= 0.4 =
* Facebook made some graph changed causing errors in Easy Facebook Feed.

= 0.3 =
* Fixed a problem with the images because of a Facebook graph change. 
* Layout bugfix in the link type post.

= 0.2 =
* Solved a small bug that occurred with older php versions.

= 0.1 =
* First release
