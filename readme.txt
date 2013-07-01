=== iQ Block Country ===
Contributors: iqpascal,williewonka
Donate link: http://www.unicef.org/
Tags: spam, block, countries, country, comments, ban, geo, geo blocking, geo ip, block country, block countries, ban countries, ban country
Requires at least: 2.9.2
Tested up to: 3.5.2
Stable tag: 1.0.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Block visitors from countries you don't want on your website. Based on which country an ip address is from.

== Description ==

If you want to block people from certain (obscure) countries that have no business visiting your
blog and perhaps only leave spam comments or other harmful actions than this is the plugin for you.

Choose which countries you want to ban from visiting your blog. Any visitors from that country get 
a HTTP/403 error with the standard message "Forbidden - Users from your country are not permitted 
to browse this site." The message is customizable.

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

= How come that I still see visitors from countries that I blocked in Statpress or other statistics software? =

It’s true that you might see hits from countries that you have blocked in your statistics software. 
That is the way it works, certain plugins may be run before iQ Block Country is run so it may log visitors to pages. 
This however does not mean this plugin does not work, it just means somebody tried to access a certain page or pages 
and that that fact is logged.

If you are worried this plugin does not work you could try to block your own country and afterwards visit your 
frontend website and see if it actually works. Also if you have access to the logfiles of the webserver that hosts 
your website  you can see that these visitors are actually denied with a HTTP error 403.

= This plugin does not work, I blocked a country and still see visitors! =

Well, this plugin does in fact work but is limited to the data MaxMind provides. Also in your statistics software or
logfiles you probably will see log entries from countries that you have blocked. See the "How come I still see visitors..."
FAQ for that.

If you think you have a visitor from a country you have blocked lookup that specific IP address on the MaxMind website
(http://www.maxmind.com/app/locate_demo_ip) and see which country MaxMind thinks it is. If this is not the same country
you may wish to block the country that MaxMind thinks it is.

= Does this plugin also work with IPv6? =

A first version of IPv6 is implemented since v1.0.7. But as IPv6 is still scarce it may not
work as well as IPv4. Some IPv6 blocks may not be in the right country in the MaxMind database.

There are no guarantees blocking IPv6 works but as far as I was able to test IPv6 blocking it
works just fine.

If you want IPv6 support be sure to press the "Download new GeoIP IPv6 database" button. At this
time the IPv6 database is not downloaded automatically.

= Does this plugin work with caching? =

In some circumstances: No

The plugin does it best to prevent caching of the "You are blocked" message. However most caching 
software can be forced to cache anyway. You may or may not be able to control the behaviour of
the caching method.

The plugin does it bests to avoid caching but under circumstances the message does get cached.
Either change the behaviour of your caching software or disable the plugin.

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

If you also need IPv6 you can download the IPv6 database on the following url:

http://geolite.maxmind.com/download/geoip/database/GeoIPv6.dat.gz

It will also give you the location it expects the GeoIP.dat file. So go ahead and download  it and unzip the file. 
Afterwards upload it to this specific location with for instance FTP,SFTP or FTPS.

= Why does downloading the GeoIP.dat.gz fail? =

For instance Maxmind limits the number of downloads per day. They do this by IP address so if you or somebody
else who has a website at the same server your site is running on already downloaded the new database you may be blocked
for 24 hours by MaxMind. If you are blocked because of too many requests this plugin tries to detect it and display
an error message that you should try again later. So no worries try a day later again.

Other possible faults are your webhosting company not allowing downloads on HTTP port 80.

If your download fails try to download it from home or work and upload it via FTP,sFTP or FTPS to the location that is displayed.

= I select "Block users from the backend of your site option and ban my own country and nothing happens =

This is "as-designed" as long as you are logged in you will not be blocked. Open another browser and see if you can login to your
backend. By designing it this way you can fix a whoops without you having to alter your database.

== Changelog ==

= 1.0.9 =

* Bugfix release. The backend was not blocked in multi-site (network) setup.

= 1.0.8 =
* Automaticly download new GeoIP updates from Maxmind. This is checked each time you login on the Wordpress admin site (williewonka)
* Also block login attempts to the wp-admin site (williewonka)
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

= 1.0.9 =

See upgrade notice from 1.0.8 if you upgrade from a release prior to 1.0.8.

= 1.0.8 =

This plugin adds some new checkbox settings so you can choose if you want to block users from your frontend website,
your backend website or both. By default only the frontend site is blocked. If you wish to alter this behaviour go
to your settings page.

= 1.0.7 =

You can now use the "Do not block users who are logged in" checkbox if you like. Also if you need IPv6 support you need 
to press the "Download new GeoIP IPv6 database".

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

