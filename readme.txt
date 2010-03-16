=== iQ Block Country ===
Contributors: iqpascal
Donate link: http://www.trinyx.nl/
Tags: spam, block, countries, country, comments, ban
Requires at least: 2.9.2
Tested up to: 2.9.2
Stable tag: 1.0.1

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

No not yet. Maxmind has a GeoIPv6 database though. And as my blog is also reachable
on IPv6 I might incoporate this database is this is possible.

However: We need more IPv6 out there so please DO ask your hosting provider for IPv6!

= How can I get a new version of the GeoIP database? =

You can remove the file GeoIP.dat from the plugin directory and after removal go to
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


== Changelog ==

= 1.0 =
* Initial release

= 1.0.1 =
* Included the necessary geoip.inc file.. *duh*



== Upgrade Notice ==

= 1.0 =
* Initial release

= 1.0.1 =
* Just upgrade it.