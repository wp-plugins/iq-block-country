<?php   
/*
Plugin Name: iQ Block Country
Plugin URI: http://www.redeo.nl/2013/12/iq-block-country-wordpress-plugin-blocks-countries/
Version: 1.1.15
Author: Pascal
Author URI: http://www.redeo.nl/
Description: Block visitors from visiting your website and backend website based on which country their IP address is from. The Maxmind GeoIP lite database is used for looking up from which country an ip address is from.
License: GPL2
*/

/* This script uses GeoLite Country from MaxMind (http://www.maxmind.com) which is available under terms of GPL/LGPL */

/*  Copyright 2010-2014  Pascal  (email : pascal@redeo.nl)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
 * 
 * This software is dedicated to my one true love.
 * Luvya :)
 * 
 */

/*
 * Try to make this plugin the first plugin that is loaded.
 * Because we output header info we don't want other plugins to send output first.
 */
function iqblockcountry_this_plugin_first() 
{
	$wp_path_to_this_file = preg_replace('/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR."/$2", __FILE__);
	$this_plugin = plugin_basename(trim($wp_path_to_this_file));
	$active_plugins = get_option('active_plugins');
	$this_plugin_key = array_search($this_plugin, $active_plugins);
	if ($this_plugin_key) { // if it's 0 it's the first plugin already, no need to continue
		array_splice($active_plugins, $this_plugin_key, 1);
		array_unshift($active_plugins, $this_plugin);
		update_option('active_plugins', $active_plugins);
	}     
}


/*
 * Attempt on output buffering to protect against headers already send mistakes 
 */
function iqblockcountry_buffer() {
	ob_start();
} 

/*
 * Attempt on output buffering to protect against headers already send mistakes 
 */
function iqblockcountry_buffer_flush() {
	ob_end_flush();
} 


/*
 * Localization
 */
function iqblockcountry_localization()
{
    load_plugin_textdomain( 'iqblockcountry', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
}

/*
 * Get list of countries from Geo Database
 */
function iqblockcountry_get_countries()
{
    global $countrylist;
    
    $countrylist = array();
    if (!class_exists('GeoIP'))
    {
	include_once("libs/geoip.inc");
    }
    if (class_exists('GeoIP'))
    {
	/* Create an array with all countries that the database knows */
	$geo = new GeoIP ();
	$countrycodes = $geo->GEOIP_COUNTRY_CODE_TO_NUMBER;
	$countries = $geo->GEOIP_COUNTRY_NAMES;
	$countrylist = array ();
	foreach ( $countrycodes as $key => $value ) {
            if (!empty($value))
            {
		$countrylist [$key] = $countries [$value];
            }
	}
	array_multisort($countrylist);

        return $countrylist;
    }

    return $countylist;
 }    

/*
  * Retrieves the IP address from the HTTP Headers
 */
function iqblockcountry_get_ipaddress() {
    global $ip_address;
    
    $ip_address = "";
    if (empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        $ip_address = $_SERVER["REMOTE_ADDR"];
    } else {
        $ip_address = $_SERVER["HTTP_X_FORWARDED_FOR"];
    }
    return $ip_address;
}

function iqblockcountry_upgrade()
{
    /* Check if update is necessary */
    $dbversion = get_option( 'blockcountry_version' );
    update_option('blockcountry_version',VERSION);

    if ($dbversion != "" && version_compare($dbversion, "1.1.14", '<') )
    {
        update_option('blockcountry_automaticupdate', 'on');
    }
    elseif ($dbversion != "" && version_compare($dbversion, "1.1.11", '<') )
    {
        update_option('blockcountry_nrstatistics', 15);
    }
    elseif ($dbversion != "" && version_compare($dbversion, "1.0.10", '<') )
    {
        $frontendbanlist = get_option('blockcountry_banlist');
        update_option('blockcountry_backendbanlist',$frontendbanlist);
        update_option('blockcountry_backendnrblocks', 0);
        update_option('blockcountry_frontendnrblocks', 0);
        update_option('blockcountry_header', 'on');
    }
    elseif ($dbversion != "" && version_compare($dbversion, "1.0.10", '=') )
    {
        iqblockcountry_install_db();
        update_option('blockcountry_backendnrblocks', 0);
        update_option('blockcountry_frontendnrblocks', 0);
        update_option('blockcountry_header', 'on');
    }        
    elseif ($dbversion == "")
    {
        iqblockcountry_install_db();
        add_option( "blockcountry_dbversion", DBVERSION );
        update_option('blockcountry_lastupdate' , 0); 
        update_option('blockcountry_blockfrontend' , 'on');
        update_option('blockcountry_version',VERSION);
        update_option('blockcountry_backendnrblocks', 0);
        update_option('blockcountry_frontendnrblocks', 0);
        update_option('blockcountry_header', 'on');
        $frontendbanlist = get_option('blockcountry_banlist');
        update_option('blockcountry_backendbanlist',$frontendbanlist);
    }    

    iqblockcountry_update_db_check();
   
}

/*
 * Include libraries
 */
require_once('libs/blockcountry-checks.php');
require_once('libs/blockcountry-settings.php');
require_once('libs/blockcountry-validation.php');
require_once('libs/blockcountry-logging.php');
require_once('libs/blockcountry-tracking.php');
require_once('libs/blockcountry-search-engines.php');
require_once('libs/blockcountry-retrieve-geodb.php');

/*
 * Main plugin works.
 */
define("CHOSENJS", plugins_url('/chosen.jquery.js', __FILE__));
define("CHOSENCSS", plugins_url('/chosen.css', __FILE__));
define("IPV6DB","http://geolite.maxmind.com/download/geoip/database/GeoIPv6.dat.gz");
define("IPV4DB","http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz");
define("IPV4DBFILE",WP_PLUGIN_DIR . "/" . dirname ( plugin_basename ( __FILE__ ) ) . "/GeoIP.dat");
define("IPV6DBFILE",WP_PLUGIN_DIR . "/" . dirname ( plugin_basename ( __FILE__ ) ) . "/GeoIPv6.dat");
define("TRACKINGURL","http://tracking.webence.nl/iq-block-country-tracking.php");
define("TRACKINGRETRIEVEURL","http://tracking.webence.nl/iq-block-country-retrieve.php");
define("VERSION","1.1.15");
define("DBVERSION","121");
define("PLUGINPATH",plugin_dir_path( __FILE__ )); 

global $apiblacklist;
$apiblacklist = FALSE;
$backendblacklistcheck = FALSE;

register_activation_hook(__file__, 'iqblockcountry_this_plugin_first');
register_activation_hook(__file__, 'iqblockcountry_set_defaults');
register_uninstall_hook(__file__, 'iqblockcountry_uninstall');

 // Check if upgrade is necessary
 iqblockcountry_upgrade();
  
 /* Clean logging database */
 iqblockcountry_clean_db();
 
/*
 * Check first if users want to block the backend.
 */
if ((iqblockcountry_is_login_page() || is_admin()) && get_option('blockcountry_blockbackend'))
{
    add_action ( 'login_head', 'iqblockcountry_checkCountry', 1 );
}
/*
 * Check first if users want to block the frontend.
 */
if (get_option('blockcountry_blockfrontend'))
{
    add_action ( 'wp_head', 'iqblockcountry_checkCountry', 1 );
}

add_action ( 'admin_init', 'iqblockcountry_localization');
add_action ( 'admin_menu', 'iqblockcountry_create_menu' );
add_action ( 'admin_init', 'iqblockcountry_checkupdatedb' );
add_filter ( 'update_option_blockcountry_tracking', 'iqblockcountry_schedule_tracking', 10, 2);
add_filter ( 'add_option_blockcountry_tracking', 'iqblockcountry_schedule_tracking', 10, 2);
add_filter ( 'update_option_blockcountry_apikey', 'iqblockcountry_schedule_retrieving', 10, 2);
add_filter ( 'add_option_blockcountry_apikey', 'iqblockcountry_schedule_retrieving', 10, 2);
//add_filter ( 'update_option_blockcountry_backendlogging', 'iqblockcountry_blockcountry_backendlogging', 10, 2);
//add_filter ( 'add_option_blockcountry_backendlogging', 'iqblockcountry_blockcountry_backendlogging', 10, 2);
add_action ( 'blockcountry_tracking', 'iqblockcountry_tracking' );
add_action ( 'blockcountry_retrievebanlist',  'iqblockcountry_tracking_retrieve_xml');
if (get_option('blockcountry_buffer') == "on")
{
    add_action ( 'init', 'iqblockcountry_buffer',1);
    add_action ( 'wp_footer', 'iqblockcountry_buffer_flush');
}

?>