<?php
/*
Plugin Name: iQ Block Country
Plugin URI: http://www.trinyx.nl/2010/03/iq-block-country-a-wordpress-plugin/
Version: 1.0.3
Author: Pascal
Author URI: http://www.trinyx.nl/
Description: Block out the bad guys based on from which country the ip address is from. This plugin uses the GeoLite data created by MaxMind for the ip-to-country lookups.
Author URI: http://www.trinyx.nl/
License: GPL2
*/

/* This script uses GeoLite Country from MaxMind (http://www.maxmind.com) which is available under terms of GPL/LGPL */

/*  Copyright 2010  Pascal  (email : pascal@trinyx.nl)

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


function iq_is_valid_ipv4($ipv4) {

	if(filter_var($ipv4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === FALSE) {
		return false;
	}

	return true;
}

function iq_is_valid_ipv6($ipv6) {

	if(filter_var($ipv6, FILTER_VALIDATE_IP,FILTER_FLAG_IPV6) === FALSE) {
		return false;
	}

	return true;
}

function iq_this_plugin_first() {
	// ensure path to this file is via main wp plugin path
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
 * Admin menu stuff
 */
function iqblockcountry_create_menu() {
	//create new menu option in the settings department
	add_submenu_page ( 'options-general.php', 'iQ Block Country', 'iQ Block Country', 'administrator', __FILE__, 'iqblockcountry_settings_page' );
	//call register settings function
	add_action ( 'admin_init', 'iqblockcountry_register_mysettings' );
}

function iqblockcountry_register_mysettings() {
	//register our settings
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_banlist' );
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_blacklist' );
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_whitelist' );
}

function iqblockcountry_settings_page() {
	?>
<div class="wrap">
<h2>iQ Block Countries</h2>

<form method="post" action="options.php">
    <?php
	settings_fields ( 'iqblockcountry-settings-group' );
	
	if (!class_exists(GeoIP))
	{
		include_once("geoip.inc");
	}
	if (class_exists(GeoIP))
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
		/* Display this array on the admin page and select any country that was already set */
		?>
    	    <table class="form-table">
			<tr valign="top">
				<th scope="row">Countries to block:<br />
				Use the ctrl key to select multiple countries</th>
				<td><select name="blockcountry_banlist[]" multiple="multiple" style="height: 450px;">
    	        <?
			$haystack = get_option ( 'blockcountry_banlist' );
			foreach ( $countrylist as $key => $value ) {
			print "<option value=\"$key\"";
			if (is_array($haystack) && in_array ( $key, $haystack )) {
				print " selected=\"selected\" ";
			}
			print ">$value</option>";
		}
		
		?>
    	        </select></td>
			</tr>
	
			</table>	
	
			<p class="submit"><input type="submit" class="button-primary"
				value="<?php _e ( 'Save Changes' )?>" /></p>
	
	<?php 
	} 
	else
	{
		print "<p>You are missing the GeoIP class. Perhaps geoip.inc is missing?</p>";	
	
	}
	?>	

	<p>This product includes GeoLite data created by MaxMind, available from
	<a href="http://www.maxmind.com/">http://www.maxmind.com/</a>.</p>

	<p>If you like this plugin please link back to <a href="http://www.trinyx.nl/">Trinyx.nl</a>! :-)</p>

    <?php
	global $geodbfile;
	
	/* Check if the Geo Database exists otherwise try to download it */
	if (! (file_exists ( $geodbfile ))) {
		?> 
		<p>GeoIP database does not exists. Trying to download it...</p>
		<?php
		
		/* GeoLite URL */
		$url = 'http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz';
		$request = new WP_Http ();
		$result = $request->request ( $url );
		$content = array ();
		
		if ((isset ( $result->errors )) || (! (in_array ( '200', $result ['response'] )))) {
			print "<p>Error occured: Could not download the GeoIP database from $url.<br />";
			print "Please download this file yourself and unzip this file to $geodbfile</p>";
		} else {
			/* Download file */
			$content = $result ['body'];
			$fp = fopen ( $geodbfile . ".gz", "w" );
			fwrite ( $fp, "$content" );
			fclose ( $fp );
			
			/* Unzip this file and throw it away afterwards*/
			$zd = gzopen ( $geodbfile . ".gz", "r" );
			$buffer = gzread ( $zd, 2000000 );
			gzclose ( $zd );
			unlink ( $geodbfile . ".gz" );
			
			/* Write this file to the GeoIP database file */
			$fp = fopen ( $geodbfile, "w" );
			fwrite ( $fp, "$buffer" );
			fclose ( $fp );
			print "<p>Finished downloading</p>";
		
		}
	}
	
	?>



</form>
</div>
<?php
}

/*
  * Country check stuff that happens at the frontpage
 */

function iqblockcountry_CheckCountry() {
	if (!class_exists(GeoIP))
	{
		include_once("geoip.inc");
	}
	global $geodbfile;
	
	if ((file_exists ( $geodbfile )) && function_exists(geoip_open)) {

		if (empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$ip_address = $_SERVER["REMOTE_ADDR"];
		} else {
			$ip_address = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}

		// First steps into being IPv6 compatible.
		
		$ipv4 = FALSE;
		$ipv6 = FALSE;
		if (iq_is_valid_ipv4($ip_address)) { $ipv4 = TRUE; }
		if (iq_is_valid_ipv6($ip_address)) { $ipv6 = TRUE; }
		
		if ($ipv4) 
		{ 	
			$gi = geoip_open ( $geodbfile, GEOIP_STANDARD );
			$country = geoip_country_code_by_addr ( $gi, $ip_address );
			geoip_close ( $gi );
		}
		elseif ($ipv6)
		{
			// Do nothing at all as we're not IPv6 compatible just yet.
			$country = 'ipv6';
		}
		
		$badcountries = get_option( 'blockcountry_banlist' );

		/* Check if we have one of those bad guys */
		if (is_array ( $badcountries ) && in_array ( $country, $badcountries )) {
			header ( 'HTTP/1.1 403 Forbidden' );
			print "<p><strong>Forbidden - Users from your country are not permitted to browse this site.<strong></p>";
			
			exit ();
		}
	
	}

}

/*
 * Main things
 */
$geodbfile = WP_PLUGIN_DIR . "/" . dirname ( plugin_basename ( __FILE__ ) ) . "/GeoIP.dat";

add_action ( "activated_plugin", "iq_this_plugin_first");
add_action ( 'wp_head', 'iqblockcountry_checkCountry', 1 );
add_action ( 'admin_menu', 'iqblockcountry_create_menu' );

?>