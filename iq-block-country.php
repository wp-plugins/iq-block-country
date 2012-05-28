<?php
/*
Plugin Name: iQ Block Country
Plugin URI: http://www.redeo.nl/2010/03/iq-block-country-a-wordpress-plugin/
Version: 1.0.7
Author: Pascal
Author URI: http://www.redeo.nl/
Description: Block out the bad guys based on from which country the ip address is from. This plugin uses the GeoLite data created by MaxMind for the ip-to-country lookups.
Author URI: http://www.redeo.nl/
License: GPL2
*/

/* This script uses GeoLite Country from MaxMind (http://www.maxmind.com) which is available under terms of GPL/LGPL */

/*  Copyright 2010-2012  Pascal  (email : pascal@redeo.nl)

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
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_blockmessage' );
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_blocklogin' );
}

function iqblockcountry_downloadgeodatabase($version) {
/*
 * Download the GeoIP database from MaxMind
 */
 /* GeoLite URL */
	
 if( !class_exists( 'WP_Http' ) )
        include_once( ABSPATH . WPINC. '/class-http.php' );

 global $geodbfile,$geodb6file;
 if ($version == 6)
 {
 	$url = 'http://geolite.maxmind.com/download/geoip/database/GeoIPv6.dat.gz';
 	$geofile = $geodb6file;
 }
 else 
 {
 	$url = 'http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz';
 	$geofile = $geodbfile;
 }       
 
 $request = new WP_Http ();
 $result = $request->request ( $url );
 $content = array ();

 if ((in_array ( '403', $result ['response'] )) && (preg_match('/Rate limited exceeded, please try again in 24 hours./', $result['body'] )) )  {
 ?>
 	<p>Error occured: Could not download the GeoIP database from <?php echo $url;?><br />
	MaxMind has blocked requests from your IP address for 24 hours. Please check again in 24 hours or download this file from your own PC<br />
    unzip this file and upload it (via FTP for instance) to:<br /> <strong><?php echo $geofile;?></strong></p>
 <?php
 }
 elseif ((isset ( $result->errors )) || (! (in_array ( '200', $result ['response'] )))) {
 ?>
 	<p>Error occured: Could not download the GeoIP database from <?php echo $url;?><br />
	Please download this file from your own PC unzip this file and upload it (via FTP for instance) to:<br /> 
	<strong><?php echo $geofile;?></strong></p>
 <?php
 } else {

//	global $geodbfile;
			
	/* Download file */
	if (file_exists ( $geofile . ".gz" )) { unlink ( $geofile . ".gz" ); }
	$content = $result ['body'];
	$fp = fopen ( $geofile . ".gz", "w" );
	fwrite ( $fp, "$content" );
	fclose ( $fp );
		
	/* Unzip this file and throw it away afterwards*/
	$zd = gzopen ( $geofile . ".gz", "r" );
	$buffer = gzread ( $zd, 2000000 );
	gzclose ( $zd );
	if (file_exists ( $geofile . ".gz" )) { unlink ( $geofile . ".gz" ); }
			
	/* Write this file to the GeoIP database file */
	if (file_exists ( $geofile )) { unlink ( $geofile ); } 
	$fp = fopen ( $geofile, "w" );
	fwrite ( $fp, "$buffer" );
	fclose ( $fp );
	print "<p>Finished downloading</p>";	
 }
 if (! (file_exists ( $geodbfile ))) {
	?> 
	<p>Fatal error: GeoIP $geodbfile database does not exists. This plugin will not work until the database file is present.</p>
	<?php
 }
 print "<hr>";
}

function iqblockcountry_settings_page() {
	?>
<div class="wrap">
<h2>iQ Block Countries</h2>

		<strong>The GeoIP database is updated once a month. Be sure to update this file every now and then by clicking the following button:</strong>
        
		<form name="download_geoip" action="#download" method="post">
        <input type="hidden" name="action" value="download" />
<?php 
        echo '<div class="submit"><input type="submit" name="test" value="' . __( 'Download new GeoIP Database', 'iq-block-country' ) . '" /></div>';
        wp_nonce_field('iq-block-country');
        echo '</form>';
?>		
		<form name="download_geoip6" action="#download6" method="post">
        <input type="hidden" name="action" value="download6" />
<?php 
        echo '<div class="submit"><input type="submit" name="test" value="' . __( 'Download new GeoIP IPv6 Database', 'iq-block-country' ) . '" /></div>';
        wp_nonce_field('iq-block-country');
        echo '</form>';
        
        if ( $_POST[ 'action' ] == 'download') {
			echo "Downloading....";	
			iqblockcountry_downloadgeodatabase('4');	
		}
        if ( $_POST[ 'action' ] == 'download6') {
			echo "Downloading....";	
			iqblockcountry_downloadgeodatabase('6');	
		}
		
        
        ?>

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
		array_multisort($countrylist);
		
		/* Display this array on the admin page and select any country that was already set */
		?>
    	    <table class="form-table">
    	    <tr valign="top">
    	    <th>Message to display when people are blocked:</th>
    	    <td>
    	    	<input type="text" size=75 name="blockcountry_blockmessage"
    	    <?php
				$blockmessage = get_option ( 'blockcountry_blockmessage' );
				if (empty($blockmessage)) { $blockmessage = "Forbidden - Users from your country are not permitted to browse this site."; }
				echo "value=\"" . $blockmessage . "\" />";
    	    ?>
    	    </td></tr>

    	    <tr valign="top">
    	    <th>Do not block users who are logged in:</th>
    	    <td>
    	    	<input type="checkbox" name="blockcountry_blocklogin"
    	    <?php
				$blocklogin = get_option ( 'blockcountry_blocklogin' );
				if ($blocklogin == "on") { print "checked"; }
				echo " />";
    	    ?>
    	    </td></tr>

    	    
			<tr valign="top">
				<th scope="row">Countries to block:<br />
				Use the ctrl key to select multiple countries</th>
				<td><select name="blockcountry_banlist[]" multiple="multiple" style="height: 450px;">
    	        <?php
			$haystack = get_option ( 'blockcountry_banlist' );
			foreach ( $countrylist as $key => $value ) {
			print "<option value=\"$key\"";
			if (is_array($haystack) && in_array ( $key, $haystack )) {
				print " selected=\"selected\" ";
			}
			print ">$value</option>\n";
			
		}
		
		?>
    	        </select></td>
			</tr>
			<tr><td></td><td>
						<p class="submit"><input type="submit" class="button-primary"
				value="<?php _e ( 'Save Changes' )?>" /></p>
			</td></tr>	
			</table>	
	
	
	<?php 
	} 
	else
	{
		print "<p>You are missing the GeoIP class. Perhaps geoip.inc is missing?</p>";	
	
	}
	?>	

	<p>This product includes GeoLite data created by MaxMind, available from
	<a href="http://www.maxmind.com/">http://www.maxmind.com/</a>.</p>

	<p>If you like this plugin please link back to <a href="http://www.redeo.nl/">redeo.nl</a>! :-)</p>

    <?php
	global $geodbfile;
	
	/* Check if the Geo Database exists otherwise try to download it */
	if (! (file_exists ( $geodbfile ))) {
		?> 
		<hr>
		<p>GeoIP database does not exists. Trying to download it...</p>
		<?php
		
			iqblockcountry_downloadgeodatabase('4');	
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
	global $geodbfile,$geodb6file;
	
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
			if (file_exists ( $geodb6file )) {				
				$gi = geoip_open($geodb6file,GEOIP_STANDARD);
				$country = geoip_country_code_by_addr_v6 ( $gi, $ip_address );
	 			geoip_close($gi);
			}
			else {
				$country = 'ipv6';				
			}
		}
		
		$badcountries = get_option( 'blockcountry_banlist' );

		$blocklogin = get_option ( 'blockcountry_blocklogin' );
		if ( ((is_user_logged_in()) && ($blocklogin != "on")) || (!(is_user_logged_in())) )  {			
			/* Check if we have one of those bad guys */
			if (is_array ( $badcountries ) && in_array ( $country, $badcountries )) {
				$blockmessage = get_option ( 'blockcountry_blockmessage' );
				header ( 'HTTP/1.1 403 Forbidden' );
				print "<p><strong>$blockmessage<strong></p>";
			
				exit ();
			}
		
		}
	
	}

}

/*
 * Main things
 */
$geodbfile = WP_PLUGIN_DIR . "/" . dirname ( plugin_basename ( __FILE__ ) ) . "/GeoIP.dat";
$geodb6file = WP_PLUGIN_DIR . "/" . dirname ( plugin_basename ( __FILE__ ) ) . "/GeoIPv6.dat";

add_action ( "activated_plugin", "iq_this_plugin_first");
add_action ( 'wp_head', 'iqblockcountry_checkCountry', 1 );
add_action ( 'admin_menu', 'iqblockcountry_create_menu' );

?>