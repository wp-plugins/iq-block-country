=== iQ Block Country ===
Contributors: iqpascal
Donate link: http://www.unicef.org/
Tags: spam, block, countries, country, comments, ban
Requires at least: 2.9.2
Tested up to: 3.3.2
Stable tag: 1.0.5

Block out the bad guys based on from which country the ip address is from.

== Description ==

If you want to block people from certain (obscure) countries that have no business visiting your
blog and perhaps only leave spam comments or other harmful actions than this is the plugin for you.

Choose which countries you want to ban from visiting your blog. Any visitors from that country get 
a HTTP/403 error with the message "Forbidden - Users from your country are not permitted to browse 
this site."

They will not be able to do harmful things to your blog like post comment spam.

This plugin uses the GeoLite database from Maxmind. It has a 99.5% accuracy so that is pretty good.

Once you setup this plugin it will try to download the GeoIP database from Maxmind so you will 
always have a recent version of the database when installing this plugin.

== Installation ==

1. Unzip the archive and put the `iq-block-country` folder into your plugins folder (/wp-content/plugins/).
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the settings page and choose which countries you want to ban. Use the ctrl key to select
multiple countries
4. Check if it downloaded the GeoIP database from MaxMind otherwise follow instructions on screen.

== Frequently Asked Questions ==

= Does this plugin also work with IPv6? =

No not yet. Maxmind has a GeoIPv6 database though just no support for PHP yet. 
And as my blog is also reachable on IPv6 I might incoporate this database as soon this is possible.

However: We need more IPv6 out there so please DO ask your hosting provider for IPv6!

= How can I get a new version of the GeoIP database? =

Since version 1.0.4 you can press the "Download new GeoIP database" from the admin page
to download a new version of the database. You do not need to download it more than
once a month since the lite database is only updated once a month.

You can also remove the file GeoIP.dat from the plugin directory and after removal go to
the settings page of this plugin. When it sees the GeoIP database is missing it will
try to download it for you.

However you can also download the GeoIP database yourself from Maxmind and overwrite
the existing database.

Maxmind updates the GeoLite database every month.

= Help it gives some error about not being able to download the GeoIP database? =

Follow the instructions on screen. It will probably tell you that you have to manually
download the GeoIP database from Maxmind from the following url:

http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz

It will also give you the location it expects the GeoIP.dat file. So go ahead and
download it and unzip the file to this specific location.

= How come that I still see visitors from countries that I blocked in Statpress or other statistics software? =

It’s true that you might see hits from countries that you have blocked in your statistics software. 
That is the way it works, certain plugins may be run before iQ Block Country is run so it may log visitors to pages. 
This however does not mean this plugin does not work, it just means somebody tried to access a certain page or pages 
and that that fact is logged.

If you are worried this plugin does not work you could try to block your own country and afterwards visit your 
frontend website and see if it actually works. Also if you have access to the logfiles of the webserver that hosts 
your website  you can see that these visitors are actually denied with a HTTP error 403.

= Why does downloading the GeoIP.dat.gz fail? =

For instance Maxmind limits the number of downloads per day. They probably do this by IP address so if you or somebody
else who has a site at the same server your site is running on already downloaded the new database you may be blocked
for 24 hours by MaxMind. No worries try a day later again.

Other possible faults are your webhosting company not allowing downloads on HTTP port 80.

If your download fails try to download it from home or work and upload it via FTP/sFTP to the location that is displayed.

== Changelog ==

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

= 1.0.5 =

None, this is just a minor update

= 1.0.4 = 

None, this is just a minor update.

= 1.0.3 = 

This baby should just upgrade fine. You may want to deactivate and reactivate this plugin to make (pretty) sure this
plugin is loaded first. This especially recommended if you have problems with 'headers already sent' notices.

= 1.0.2 =
PHP 5.2 or higher is now required.

Just upgrade if you have PHP 5.2 or higher.

= 1.0.1 =
Critical upgrade for this plugin to work as it was missing a necessary file.

