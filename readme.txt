=== iQ Block Country ===
Contributors: iqpascal
Donate link: http://www.redeo.nl/plugins/donate
Tags: spam, block, countries, country, comments, ban, geo, geo blocking, geo ip, block country, block countries, ban countries, ban country, blacklist, whitelist
Requires at least: 3.5.2
Tested up to: 4.1
Stable tag: 1.1.18
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Block visitors from countries you don't want on your website. Based on which country your visitor is from.

== Description ==

If you want to block people from certain (obscure) countries that have no business visiting your
blog and perhaps only leave spam comments or other harmful actions than this is the plugin for you.

Choose which countries you want to ban from visiting your blog. Any visitors from that country get 
a HTTP/403 error with the standard message "Forbidden - Users from your country are not permitted 
to browse this site." The message is customizable an can be styled with CSS.

If you only want to block other countries from visiting your backend (administrator) website than this plugin is also something for you.

If you want to block users from both your frontend website as well as your backend website than this plugin is really something for you!

Users that are blocked will not be able to do harmful things to your blog like post comment spam.

You can block all visitors from a certain country accessing your site but you can also limit access to some pages, or some blog categories.

This plugin uses the GeoLite database from Maxmind. It has a 99.5% accuracy so that is pretty good for a free database. If you need higher accuracy you can buy a license from MaxMind directly.
If you cannot or do not want to download the GeoIP database from Maxmind you can use the GeoIP API website available on http://geoip.webence.nl/

If you want to use the GeoLite database from Maxmind you will have to download the GeoIP database from MaxMind directly and upload it to your site.
The Wordpress license does not allow this plugin to download the MaxMind Geo database for you.

== Installation ==

1. Unzip the archive and put the `iq-block-country` folder into your plugins folder (/wp-content/plugins/).
2. Download the IPv4 database from: http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz
3. Unzip the GeoIP database and upload it to your upload dir usually /wp-content/uploads/GeoIP.dat
4. Download the IPv6 database if you have a website running on IPv6 from: http://geolite.maxmind.com/download/geoip/database/GeoIPv6.dat.gz
5. Unzip the GeoIP database and upload it to your upload dir usually /wp-content/uploads/GeoIPv6.dat
6. If you do not want to or cannot download the MaxMind GeoIP database you can use the GeoIP API.
7. Activate the plugin through the 'Plugins' menu in WordPress
8. Go to the settings page and choose which countries you want to ban. Use the ctrl key to select multiple countries

== Frequently Asked Questions ==

= Why is the GeoLite database not downloaded anymore ? =

The Wordpress guys have contacted me that the license of the MaxMind GeoLite database and the Wordpress license conflicted. So it was no longer
allowed to include the GeoLite database or provide an automatic download or download button. Instead users should download the database themselves
and upload them to the website.

Wordpress could be held liable for any license issue. So that is why the auto download en update was removed from this plugin.

= How come that I still see visitors from countries that I blocked in Statpress or other statistics software? =

It’s true that you might see hits from countries that you have blocked in your statistics software. 
That is the way it works, certain plugins may be run before iQ Block Country is run so it may log visitors to pages. 

This however does not mean this plugin does not work, it just means somebody tried to access a certain page or pages and that that fact is logged.

If you are worried this plugin does not work you could try to block your own country or your own ip address and afterwards visit your frontend website and see if it actually works. Also if you have access to the logfiles of the webserver that hosts your website  you can see that these visitors are actually denied with a HTTP error 403.

= This plugin does not work, I blocked a country and still see visitors! =

Well, this plugin does in fact work but is limited to the data MaxMind provides. Also in your statistics software or logfiles you probably will see log entries from countries that you have blocked. See the "How come I still see visitors..." FAQ for that.

If you think you have a visitor from a country you have blocked lookup that specific IP address on the tools tab and see which country MaxMind thinks it is. If this is not the same country you may wish to block the country that MaxMind thinks it is.

= Whoops I made a whoops and blocked my own country from visiting the backend. Now I cannot login... HELP! =

I am afraid this can only be solved by editing your MySQL database,directly editing the rows in the wp_options table. You can use a tool like PHPMyAdmin for that.

If you don't know how to do this please ask your hosting provider if they can help, or ask me if I can help you out!

= Why do you not make something that can override that it blocks my country from the backend. =

Well, if you can use a manual override so can the people that want to 'visit' your backend. 

This plugin is meant to keep people out. Perhaps you keep a key to your house somewhere hidden in your garden but this plugin does not have a key somewhere hidden... So if you locked yourself out you need to call a locksmith (or pick the lock yourself of course!)

= How can I style the banned message? =

You can style the message by using CSS in the textbox. You are also able to include images, so you could visualize that people are banned from your site.

You can also provide a link to another page explaining why they might be banned. Only culprit is that it cannot be a page on the same domain name as people would be banned from that page as well.

You can use for instance:

<style type="text/css">
  body {
    color: red;
    background-color: #ffffff; }
    h1 {
    font-family: Helvetica, Geneva, Arial,
          SunSans-Regular, sans-serif }
  </style>

<h1>Go away!</h1>

you basicly can use everything as within a normal HTML page. Including images for instance.

= Does this plugin also work with IPv6? =

Since v1.0.7 this plugin supports IPv6. But as IPv6 is still scarce it may not work as well as IPv4. 
Some IPv6 blocks may not be in the right country in the MaxMind database.

There are no guarantees blocking IPv6 works but as far as I was able to test IPv6 blocking it
works just fine.

= Does this plugin work with caching? =

In some circumstances: No

The plugin does it best to prevent caching of the "You are blocked" message. However most caching software can be forced to cache anyway. You may or may not be able to control the behavior of the caching method.

The plugin does it bests to avoid caching but under circumstances the message does get cached.
Either change the behavior of your caching software or disable the plugin.

= How can I select multiple countries at once? =

You can press the CTRL key and select several countries.

Perhaps also a handy function is that you can type in a part of the name of the country!

= How can I get a new version of the GeoIP database? =

You can download the database(s) directly from MaxMind and upload them to your website.

1. Download the IPv4 database from: http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz
2. Unzip the GeoIP database and upload it to your upload dir usually /wp-content/uploads/GeoIP.dat
3. Download the IPv6 database if you have a website running on IPv6 from: http://geolite.maxmind.com/download/geoip/database/GeoIPv6.dat.gz
4. Unzip the GeoIP database and upload it to your upload dir usually /wp-content/uploads/GeoIPv6.dat

Maxmind updates the GeoLite database every month.

= I get "Cannot modify header information - headers already sent" errors =

This is possible if another plugin or your template sends out header information before this plugin does. You can deactivate and reactivate this plugin, it will try to load as the first plugin upon activation.

If this does not help you out deselectt "Send headers when user is blocked". This will no longer send headers but only display the block message. This however will mess up your website if you use caching software for your website.

= What data get sends to you when I select "Allow tracking"? =

If you select this option each hour the plugin checks if it has new data to send back to the central server. 

This data consists of each IP address that has tried to login to your backend and how many attempts were made since the last check.

Goal of this feature is to check if we can create a user-driven database of rogue IP addresses that try to login to the backend.
If this is viable in a future version this database can be used to block these rogue users despite the country they come from.

If storing or sharing an IP address is illegal in your country do not select this feature.

= The laws in my country do not allow storing IP addresses as it is personal information. =

You can select the option on the home tab "Do not log IP addresses" to stop iQ Block Country from logging IP addresses. This will however also break the statistics.

== Changelog ==

= 1.1.18 =

* Changed working directory for the GeoIP database to /wp-content/uploads

= 1.1.17 =

* Due to a conflict of the license where Wordpress is released under and the license the MaxMind databases are released under I was forced to remove all auto downloads of the GeoIP databases. You now have to manually download the databases and upload them yourself.
* Added Webence GeoIP API lookup. See http://geoip.webence.nl/ for more information about this API.

= 1.1.16 =

* New: Accessibility option. You can now choose if you want the country default selectbox or an normal selectbox.
* New: New button to empty the logging database..
* New: You can now set the option to not log the ip addresses to the database. This does not influence the blocking process only the logging process. This can be handy if the laws in your country do not permit you to log this information or if you choose not to log this information

= 1.1.15 =

* Bugfix: You can now set an option to buffer the output of the iQ Block Country plugin. If you use for instance NextGen Gallery you should not set this option as it will break uploading pictures to your gallery.
* Bugfix: Last time GeoIP databases were downloaded was wrong.
* Bugfix: If you configured auto-update of the GeoIP databases the tools tab showed that you did not configure auto update.
* Added check for HTTP_X_TM_REMOTE_ADDR to get real ip address of T-Mobile users.
* Added Twitter, Bitly, Cliqz and TinEye to the search engines list.
* New: No longer blocks category pages of categories you have not blocked.
* Bugfix: Added check if HTTP_USER_AGENT is set.

= 1.1.14 =

* Bugfix: The plugin did not recognise the login page when installed to a subdirectory.
* New: You can configure if it auto updates the GeoIP Database. Upon request of those people who have the paid database of MaxMind.
* Added Facebook and MSN to list of search engines.
* Changed the version of the geoip.inc file to the version of https://github.com/daigo75/geoip-api-php

= 1.1.13 =

* Bugfix on setting defaults when they values already existed.
* You can now allow search engines access to your country even if they come from countries that you want to block.

= 1.1.12 = 

* Bugfix on the backend blacklist / whitelist

= 1.1.11 =

* Added select box on how many rows to display on the logging tab
* Redirect blocked users to a specific page instead of displaying the block message.
* Added blacklist and whitelist of IP addresses to the backend.
* Adjusted some text
* Minor bugfixes

= 1.1.10 =

* Small fixes
* WP 3.9 compatability issue fixed

= 1.1.9 =

* Bugfix release due to faulty v1.1.8 release. My Apologies.

= 1.1.8 =

* Smashed a bug where the homepage was unprotected due to missing check.

= 1.1.7 =

* Added Russian (ru_RU) translation by Maxim
* Added Serbo-Croatian (sr_RU) translation by Borisa Djuraskovic (Webostinghub)
* Changed the logging table a bit.

= 1.1.6 =
* Added to ban categories. This works the same way as blocking pages (By request of FVCS)
* Changed the admin page layout. Added tabs for frontend and backend blocking to make it look less cluttered
* Added optional tracking to the plugin. This is an experiment to see if building a database of IP addresses that try to login to the backend is viable.
* Upon first activation the plugin now fills the backend block list with all countries except the country that is currently used to activate.
* Added IP checking in header HTTP_CLIENT_IP and HTTP_X_REAL_IP

= 1.1.5 =

* Statistics required wp-config.php in a specific place bug smashed.

= 1.1.4 =

* Added import/export function.
* Minor bugs solved

= 1.1.3 = 

* Fixed error that when using the option to block individual pages all visitors would be blocked. (Thanks to apostlepoe for reporting)

= 1.1.2 =

* Fixed localization error. (Thanks to Lisa for reporting)

= 1.1.1 =

* You can now choose to block individual pages. Leaving other pages open for visitors from blocked countries. You can for instance use this feature to block countries from visiting specific pages due to content rights etc.
* Source now supports localization. Included is the English and Dutch language. I'd be happy to include other translations if anyone can supply those to me.

= 1.1 =

* Added statistics to the plugin.
* You can view the last 15 hosts that were blocked including the url they visited.
* You can view the top 15 of countries that were blocked in the past 30 days.
* You can view the top 15 of hosts that were blocked in the past 30 days.
* You can view the top URL's that were most blocked in the past 30 days.

= 1.0.12 =

* The block message size box is now larger so there is more room for styling the message.
* Whitelist of IPvX IP addresses for the frontend. Use a semicolon to separate IP addresses.
* Blacklist of IPvX IP addresses for the frontend. Use a semicolon to separate IP addresses.

= 1.0.11 =

* You are now able to lookup which country belongs to an ip address in the backend. If the IP address is from a country that is banned this will be displayed.
* New way of selecting countries you wish to block upon multiple request. The selection box is now in sort of facebook style.
* Choose if you want to sent out headers or not. For people who get "Cannot modify header information - headers already sent" errors.
* Counter added for how many visitors where blocked from frontend and backend website.
* Code cleanup

= 1.0.10 =

* You can select different countries to block from your frontend website and your backend website.
* Made it more visible what IP you are logged in from, which country it is from and that you should not block your own country from your backend site.
* Minor changes to the settings page.
* A bit of code cleanup for future improvements.

= 1.0.9 =

* Bugfix release. The backend was not blocked in multi-site (network) setup.

= 1.0.8 =
* Automatically download new GeoIP updates from Maxmind. This is checked each time you login on the Wordpress admin site (Code by williewonka)
* Also block login attempts to the wp-admin site (Code by williewonka)
* Send no cache headers with the output.

= 1.0.7 =
* The plugin now detects if your IP address is blocked by MaxMind when downloading the GeoIP database and if so has an adjusted error message.
* New option: New checkbox to allow you to not block users that are logged in despite if they come from a blocked country. Use wisely :-)
* First version of IPv6 support.
* New Download IPv6 database button. Press "Download new GeoIP IPv6 database" if you need IPv6 support.

= 1.0.6 =
* Fixed error when not being able to download the GeoIP.dat.gz file from Maxmind it would not display the correct path.

= 1.0.5 =
* Corrected php opening tags (Reported by Phil from msiii.net)
* Sorted list of countries (As suggested by Phil from msiii.net)
* You can now customize the message that users get when they are blocked.
* We moved from http://www.trinyx.nl/ to http://www.redeo.nl/. Please update your links :-)

= 1.0.4 =
* Added a button to download the new GeoIP database.

= 1.0.3 =
* FAQ updated
* Try to make sure this plugin is loaded first to avoid "headers already sent" trouble.

= 1.0.2 =
* PHP 5.2 or higher required
* Fixed an include bug when other plugins also use the MaxMind database. Thanks to Marcus from LunaWorX for finding this bug.

= 1.0.1 =
* Included the necessary geoip.inc file.. *duh*

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.1.18 =

This plugin no longer downloads the MaxMind database. You have to download manually or use the GeoIP API.