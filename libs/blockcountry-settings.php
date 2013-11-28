<?php

/*
 * Create the wp-admin menu for iQ Block Country
 */
function iqblockcountry_create_menu() 
{
	//create new menu option in the settings department
	add_submenu_page ( 'options-general.php', 'iQ Block Country', 'iQ Block Country', 'administrator', __FILE__, 'iqblockcountry_settings_page' );
	//call register settings function
	add_action ( 'admin_init', 'iqblockcountry_register_mysettings' );
}

/*
 * Register all settings.
 */
function iqblockcountry_register_mysettings() 
{
	//register our settings
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_banlist' );
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_backendbanlist' );
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_backendblacklist','iqblockcountry_validate_ip');
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_backendwhitelist','iqblockcountry_validate_ip');
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_frontendblacklist','iqblockcountry_validate_ip');
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_frontendwhitelist','iqblockcountry_validate_ip');
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_blockmessage' );
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_blocklogin' );
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_blockfrontend' );
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_blockbackend' );
        register_setting ( 'iqblockcountry-settings-group', 'blockcountry_header');
}

/*
 * Set default values when activating this plugin.
 */
function iqblockcountry_set_defaults() 
{
    
        update_option('blockcountry_version',VERSION);
        update_option('blockcountry_lastupdate' , 0);
        update_option('blockcountry_blockfrontend' , 'on');
	update_option('blockcountry_backendnrblocks', 0);
	update_option('blockcountry_frontendnrblocks', 0);
	update_option('blockcountry_header', 'on');
        
}


function iqblockcountry_uninstall() //deletes all the database entries that the plugin has created
{
    	delete_option('blockcountry_banlist' );
	delete_option('blockcountry_backendbanlist' );
	delete_option('blockcountry_backendblacklist' );
	delete_option('blockcountry_backendwhitelist' );
	delete_option('blockcountry_frontendblacklist' );
	delete_option('blockcountry_frontendwhitelist' );
	delete_option('blockcountry_blockmessage' );
	delete_option('blockcountry_backendnrblocks' );
	delete_option('blockcountry_frontendnrblocks' );
	delete_option('blockcountry_blocklogin' );
	delete_option('blockcountry_blockfrontend' );
	delete_option('blockcountry_blockbackend' );
        delete_option('blockcountry_lastupdate');
        delete_option('blockcountry_version');
        delete_option('blockcountry_header');
}



/*
 * Create the settings page.
 */
function iqblockcountry_settings_page() {
	?>
        <div class="wrap">
<h2>iQ Block Countries</h2>

        <hr />
        <h3>Check which country belongs to an IP Address according to the current database.</h3>
   
	<form name="ipcheck" action="#ipcheck" method="post">
        <input type="hidden" name="action" value="ipcheck" />
        IP Address to check: <input type="text" name="ipaddress" lenth="50" />
<?php 
        if ( isset($_POST['action']) && $_POST[ 'action' ] == 'ipcheck') {
                    if (isset($_POST['ipaddress']) && !empty($_POST['ipaddress']))
                    {
                        $ip_address = $_POST['ipaddress'];
                        $country = iqblockcountry_check_ipaddress($ip_address);
                        $countrylist = iqblockcountry_get_countries();
                        if ($country == "Unknown" || $country == "ipv6" || $country == "")
                        {
                            echo "<p>No country for $ip_address could be found. Or $ip_address is not a valid IPv4 or IPv6 IP address</p>";
                        }
                        else {
                            $displaycountry = $countrylist[$country];
                            echo "<p>IP Adress $ip_address belongs to $displaycountry.</p>";
                            $haystack = get_option('blockcountry_banlist');
                            if (is_array($haystack) && in_array ( $country, $haystack )) {
				print "This country is not permitted to visit the frontend of this website.<br />";
                            }
                            $haystack = get_option('blockcountry_backendbanlist');
                            if (is_array($haystack) && in_array ( $country, $haystack )) {
				print "This country is not permitted to visit the backend of this website.<br />";
                            }
                        }
                    }    
		}
        echo '<div class="submit"><input type="submit" name="test" value="' . __( 'Check IP address', 'iq-block-country' ) . '" /></div>';
        wp_nonce_field('iq-block-country');
?>		
        </form>
        
        
        
<hr />
<h3>Statistics</h3>

<?php                     $blocked = get_option('blockcountry_backendnrblocks'); ?>
<p><?php echo $blocked; ?> visitors blocked from the backend.</p>
<?php                     $blocked = get_option('blockcountry_frontendnrblocks'); ?>
<p><?php echo $blocked; ?> visitors blocked from the frontend.</p>

<hr />

<h3>Basic Options</h3>
        
<form method="post" action="options.php">
    <?php
	settings_fields ( 'iqblockcountry-settings-group' );
    if (!class_exists('GeoIP'))
	{
		include_once("geoip.inc");
	}
	if (class_exists('GeoIP'))
	{
		
            $countrylist = iqblockcountry_get_countries();

            $ip_address = iqblockcountry_get_ipaddress();
            $country = iqblockcountry_check_ipaddress($ip_address);
            if ($country == "Unknown" || $country == "ipv6" || $country == "")
            { $displaycountry = "Unknown"; }
            else { $displaycountry = $countrylist[$country]; }
            
            
	?>

            <script language="javascript" type="text/javascript" src=<?php echo "\"" . CHOSENJS . "\""?>></script>
            <link rel="stylesheet" href=<?php echo "\"" . CHOSENCSS . "\""?> type="text/css" />
            <script>
                        jQuery(document).ready(function(){
			jQuery(".chosen").data("placeholder","Select country...").chosen();
                       });
            </script>
    

            <table class="form-table" cellspacing="2" cellpadding="5" width="100%">    	    

            <tr valign="top">
    	    <th width="30%">Message to display when people are blocked:</th>
    	    <td width="70%">
    	    <?php
				$blockmessage = get_option ( 'blockcountry_blockmessage' );
				if (empty($blockmessage)) { $blockmessage = "Forbidden - Users from your country are not permitted to browse this site."; }
    	    ?>
                <textarea cols="100" rows="3" name="blockcountry_blockmessage"><?php echo $blockmessage; ?></textarea>
    	    </td></tr>

    	    <tr valign="top">
    	    <th width="30%">Do not block users that are logged in from visiting frontend website:</th>
    	    <td width="70%">
    	    	<input type="checkbox" name="blockcountry_blocklogin" <?php checked('on', get_option('blockcountry_blocklogin'), true); ?> />
    	    </td></tr>

            <tr valign="top">
            <th width="30%">Block users from visiting the frontend of your website:</th>
            <td width="70%">
    	    	<input type="checkbox" name="blockcountry_blockfrontend" <?php checked('on', get_option('blockcountry_blockfrontend'), true); ?> />
            </td></tr>
            
            <tr valign="top">
		<th scope="row" width="30%">Select the countries that should be blocked from visiting your frontend:<br />
				Use the CTRL key to select multiple countries</th>
		<td width="70%">
                     <select class="chosen" name="blockcountry_banlist[]" multiple="true" style="width:600px;">
                    <?php
			$haystack = get_option('blockcountry_banlist');
			foreach ( $countrylist as $key => $value ) {
			print "<option value=\"$key\"";
			if (is_array($haystack) && in_array ( $key, $haystack )) {
				print " selected=\"selected\" ";
			}
                            print ">$value</option>\n";
                        }   
                        ?>
                     </select>
                </td></tr>
            <tr valign="top">
                <th width="30%">Frontend whitelist IPv4 and/or IPv6 addresses:<br />Use a semicolon (;) to separate IP addresses</th>
    	    <td width="70%">
    	    <?php
				$frontendwhitelist = get_option ( 'blockcountry_frontendwhitelist' );
    	    ?>
                <textarea cols="70" rows="5" name="blockcountry_frontendwhitelist"><?php echo $frontendwhitelist; ?></textarea>
    	    </td></tr>
            <tr valign="top">
                <th width="30%">Frontend blacklist IPv4 and/or IPv6 IP addresses:<br />Use a semicolon (;) to separate IP addresses</th>
    	    <td width="70%">
    	    <?php
				$frontendblacklist = get_option ( 'blockcountry_frontendblacklist' );
    	    ?>
                <textarea cols="70" rows="5" name="blockcountry_frontendblacklist"><?php echo $frontendblacklist; ?></textarea>
    	    </td></tr>
    	    <tr valign="top">
    	    <th width="30%">Block users from visiting the backend (administrator) of your website:</th>
    	    <td width="70%">
    	    	<input type="checkbox" name="blockcountry_blockbackend" <?php checked('on', get_option('blockcountry_blockbackend'), true); ?> />
            </td></tr>    
            <tr>
                <th width="30%"></th>
                <th width="70%">
                   Your IP address is <i><?php echo $ip_address ?></i>. The country that is listed for this IP address is <em><?php echo $displaycountry ?></em>.<br />  
                      Do <strong>NOT</strong> set the 'Block users from visiting the backend (administrator) of your website' and also select <?php echo $displaycountry ?> below.<br /> 
                      <strong>You will NOT be able to login the next time if you DO block your own country from visiting the backend.</strong>
                </th>
            </tr>
    	    </td></tr>
            <tr valign="top">
		<th scope="row" width="30%">Select the countries that should be blocked from visiting your backend:<br />
                Use the x behind the country to remove a country from this blocklist.</th>
		<td width="70%">
        
                    <select class="chosen" name="blockcountry_backendbanlist[]" multiple="true" style="width:600px;">
                    <?php
			$haystack = get_option ( 'blockcountry_backendbanlist' );
			foreach ( $countrylist as $key => $value ) {
			print "<option value=\"$key\"";
			if (is_array($haystack) && in_array ( $key, $haystack )) {
				print " selected=\"selected\" ";
			}
                            print ">$value</option>\n";
                        }   
                        ?>
                     </select>
                </td></tr>
    	    <tr valign="top">
    	    <th width="30%">Send headers when user is blocked:<br />
                <em>Under normal circumstances you should keep this selected! Only if you have "Cannot modify header information - headers already sent" errors or if you know what you are doing uncheck this.</em></th>
    	    <td width="70%">
    	    	<input type="checkbox" name="blockcountry_header" <?php checked('on', get_option('blockcountry_header'), true); ?> />
    	    </td></tr>
                        
		<tr><td></td><td>
						<p class="submit"><input type="submit" class="button-primary"
				value="<?php _e ( 'Save Changes' )?>" /></p>
		</td></tr>	
		</table>	
        </form>
        
        <hr />
        <h3>Download GeoIP database</h3>
        <?php
        $dateformat = get_option('date_format');
        $time = get_option('blockcountry_lastupdate');
        
        $lastupdated = date($dateformat,$time);
        
	echo "<strong>The GeoIP database is updated once a month. Last update: " . $lastupdated . ".</strong>.<br /> 
            If you need a manual update please press buttons below to update.";
        ?>
        
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
        
        if ( isset($_POST['action']) && $_POST[ 'action' ] == 'download') {
			echo "Downloading....";	
			iqblockcountry_downloadgeodatabase('4', true);	
		}
        if ( isset($_POST['action']) && $_POST[ 'action' ] == 'download6') {
			echo "Downloading....";	
			iqblockcountry_downloadgeodatabase('6', true);	
		}
		
        
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
	/* Check if the Geo Database exists otherwise try to download it */
	if (! (file_exists ( IPV4DBFILE ))) {
		?> 
		<hr>
		<p>GeoIP database does not exists. Trying to download it...</p>
		<?php
		
			iqblockcountry_downloadgeodatabase('4', true);	
			iqblockcountry_downloadgeodatabase('6', true);	
		}
	
}

